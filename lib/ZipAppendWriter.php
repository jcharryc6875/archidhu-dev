<?php

/**
 * ZipAppendWriter
 * escribe un zip ZIP64 sin compresion (STORE) agregando archivos en varias peticiones HTTP:
 * cada addFile() anexa la entrada al final del archivo y la registra en un indice; finish() solo escribe
 * el directorio central, por lo que el cierre no depende de cuantos archivos ni cuantos GB se agregaron.
 * Requiere PHP de 64 bits (pack 'P').
 */
class ZipAppendWriter
{
	const BUFFER_SIZE = 1048576;
	const PART_FILE = 'archivos.part';
	const INDEX_FILE = 'index.dat';

	private $dir;
	private $handle = null;

	/**
	 * @param string $dir carpeta del lote donde se acumulan el zip parcial y su indice
	 */
	public function __construct($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * bloquea el zip parcial; evita que dos peticiones del mismo lote (p. ej. un reintento) escriban a la vez
	 * @return bool
	 */
	public function open()
	{
		simad_util::createPath($this->dir);
		$this->handle = fopen($this->dir . DIRECTORY_SEPARATOR . self::PART_FILE, 'c+b');
		if (!$this->handle || !flock($this->handle, LOCK_EX)) {
			$this->handle = null;
			return false;
		}
		return true;
	}

	public function close()
	{
		if ($this->handle) {
			flock($this->handle, LOCK_UN);
			fclose($this->handle);
			$this->handle = null;
		}
	}

	/**
	 * @return array nombres ya agregados (clave) para no duplicar entradas ante reintentos
	 */
	public function getNames()
	{
		$names = array();
		foreach ($this->readIndex() as $entry) {
			$names[$entry['name']] = true;
		}
		return $names;
	}

	/**
	 * anexa un archivo al zip sin comprimirlo: lo copia en bloques calculando el CRC32 en la misma lectura
	 * @param string $path ruta fisica del archivo
	 * @param string $name nombre de la entrada dentro del zip
	 * @return bool
	 */
	public function addFile($path, $name)
	{
		if (!$this->handle || !is_file($path) || !is_readable($path)) {
			return false;
		}
		$source = fopen($path, 'rb');
		if (!$source) {
			return false;
		}
		if (!mb_check_encoding($name, 'UTF-8')) {
			$name = mb_convert_encoding($name, 'UTF-8', 'Windows-1252');
		}
		//******************************************************************************************
		fseek($this->handle, 0, SEEK_END);
		$offset = ftell($this->handle);
		$mtime = filemtime($path);
		fwrite($this->handle, self::localHeader($name, $mtime, 0, 0));
		//******************************************************************************************
		$hash = hash_init('crc32b');
		$size = 0;
		while (!feof($source)) {
			$buffer = fread($source, self::BUFFER_SIZE);
			$length = strlen($buffer);
			if ($length == 0) {
				break;
			}
			hash_update($hash, $buffer);
			if (fwrite($this->handle, $buffer) !== $length) {
				fclose($source);
				return false;
			}
			$size += $length;
		}
		fclose($source);
		$crc = hexdec(hash_final($hash));
		//******************************************************************************************
		// la cabecera se reescribe con el CRC y tamano reales; tiene la misma longitud que el borrador
		fseek($this->handle, $offset);
		fwrite($this->handle, self::localHeader($name, $mtime, $crc, $size));
		fseek($this->handle, 0, SEEK_END);
		list($time, $date) = self::dosTime($mtime);
		$line = implode74("\t", array(base64_encode($name), $crc, $size, $offset, $time, $date)) . "\n";
		return file_put_contents($this->dir . DIRECTORY_SEPARATOR . self::INDEX_FILE, $line, FILE_APPEND) !== false;
	}

	/**
	 * escribe el directorio central ZIP64, mueve el zip a $pathzip y elimina la carpeta del lote
	 * @param string $dir carpeta del lote
	 * @param string $pathzip ruta del zip final
	 * @return int cantidad de entradas del zip, 0 si no habia archivos o fallo
	 */
	public static function finish($dir, $pathzip)
	{
		$writer = new self($dir);
		$entries = $writer->readIndex();
		$part = $dir . DIRECTORY_SEPARATOR . self::PART_FILE;
		$count = count74($entries);
		$ok = $count > 0 && is_file($part) && $writer->open();
		if ($ok) {
			fseek($writer->handle, 0, SEEK_END);
			$cd_offset = ftell($writer->handle);
			$cd_size = 0;
			foreach ($entries as $entry) {
				$record = self::centralRecord($entry);
				fwrite($writer->handle, $record);
				$cd_size += strlen($record);
			}
			$zip64_offset = $cd_offset + $cd_size;
			fwrite($writer->handle, pack('VPvvVVPPPP', 0x06064b50, 44, 45, 45, 0, 0, $count, $count, $cd_size, $cd_offset));
			fwrite($writer->handle, pack('VVPV', 0x07064b50, 0, $zip64_offset, 1));
			fwrite($writer->handle, pack('VvvvvVVv', 0x06054b50, 0, 0, 0xFFFF, 0xFFFF, 0xFFFFFFFF, 0xFFFFFFFF, 0));
			$writer->close();
			$ok = rename($part, $pathzip) || (copy($part, $pathzip) && unlink($part));
		}
		//******************************************************************************************
		foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $file) {
			unlink($file);
		}
		rmdir($dir);
		return $ok ? $count : 0;
	}

	private function readIndex()
	{
		$entries = array();
		$index = $this->dir . DIRECTORY_SEPARATOR . self::INDEX_FILE;
		$lines = is_file($index) ? file($index, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : array();
		foreach ($lines as $line) {
			$parts = explode("\t", $line);
			if (count74($parts) == 6) {
				$entries[] = array('name' => base64_decode($parts[0]), 'crc' => (int) $parts[1], 'size' => (int) $parts[2],
					'offset' => (int) $parts[3], 'time' => (int) $parts[4], 'date' => (int) $parts[5]);
			}
		}
		return $entries;
	}

	private static function dosTime($mtime)
	{
		$d = getdate($mtime);
		if ($d['year'] < 1980) {
			return array(0, 0x21);
		}
		return array(($d['hours'] << 11) | ($d['minutes'] << 5) | (int) ($d['seconds'] / 2), (($d['year'] - 1980) << 9) | ($d['mon'] << 5) | $d['mday']);
	}

	private static function localHeader($name, $mtime, $crc, $size)
	{
		list($time, $date) = self::dosTime($mtime);
		$extra = pack('vvPP', 0x0001, 16, $size, $size);
		return pack('VvvvvvVVVvv', 0x04034b50, 45, 0x0800, 0, $time, $date, $crc, 0xFFFFFFFF, 0xFFFFFFFF, strlen($name), strlen($extra)) . $name . $extra;
	}

	private static function centralRecord($entry)
	{
		$extra = pack('vvPPP', 0x0001, 24, $entry['size'], $entry['size'], $entry['offset']);
		return pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 45, 45, 0x0800, 0, $entry['time'], $entry['date'], $entry['crc'], 0xFFFFFFFF, 0xFFFFFFFF,
			strlen($entry['name']), strlen($extra), 0, 0, 0, 0, 0xFFFFFFFF) . $entry['name'] . $extra;
	}
}
