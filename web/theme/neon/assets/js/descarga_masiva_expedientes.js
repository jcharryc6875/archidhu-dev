/**
 * Descarga masiva de documentos de expedientes marcados.
 * Comprime un expediente por peticion (cada una corta, sin riesgo de timeout) y al terminar
 * pide un unico zip que reune los zip de todos los expedientes.
 * Requiere el contenedor #descarga-masiva (ver unidad_documental/downloadBatchDocsSuccess).
 */
(function ($) {
	$(function () {
		var $box = $('#descarga-masiva');
		if (!$box.length) {
			return;
		}
		var urlExpediente = $box.data('url-expediente');
		var urlFinish = $box.data('url-finish');
		var lote = $box.data('lote');
		var $rows = $box.find('tr[data-id]');
		var $bar = $box.find('.progress-bar');
		var $aviso = $box.find('.descarga-aviso');
		var total = $rows.length;
		var processed = 0;
		var okCount = 0;
		var failed = 0;

		function setEstado($row, cls, text) {
			$row.find('.descarga-estado').attr('class', 'descarga-estado text-center ' + cls).text(text);
		}

		function setAviso(type, html) {
			$aviso.attr('class', 'alert alert-' + type + ' descarga-aviso').html(html);
		}

		function updateProgress() {
			var pct = total ? Math.round((processed / total) * 100) : 100;
			$bar.css('width', pct + '%').text(processed + ' / ' + total);
		}

		function finish() {
			if (okCount === 0) {
				$bar.removeClass('active').addClass('progress-bar-danger');
				setAviso('danger', '<strong>No se gener&oacute; ning&uacute;n archivo.</strong> Ya puede cerrar esta ventana e intentarlo de nuevo.');
				toastr.error('No se genero ningun archivo.');
				return;
			}
			setAviso('warning', '<strong>Casi listo:</strong> reuniendo los archivos en un solo zip. No cierre esta ventana.');
			$.LoadingStructData('Compactando el archivo final, por favor espere...');
			$.ajax({
				type: 'POST',
				url: urlFinish,
				data: { lote: lote },
				dataType: 'json',
				cache: false
			}).done(function (data) {
				if (data.httpStatus === 200 && data.url_descarga) {
					$bar.removeClass('active').addClass('progress-bar-success');
					var msg = 'Archivo generado.' + (failed ? ' ' + failed + ' expediente(s) con error.' : '');
					setAviso('success', '<strong>' + msg + '</strong> Use el bot&oacute;n "Descargar ZIP" si la descarga no inicia sola. Ya puede cerrar la ventana.');
					$box.find('.descarga-link').attr('href', data.url_descarga).removeClass('hidden');
					toastr.success(msg);
					window.open(data.url_descarga, '_blank');
				} else {
					$bar.removeClass('active').addClass('progress-bar-danger');
					setAviso('danger', '<strong>' + data.message + '</strong>');
					toastr.error(data.message);
				}
			}).fail(function () {
				$bar.removeClass('active').addClass('progress-bar-danger');
				setAviso('danger', '<strong>Ocurri&oacute; un error generando el archivo final.</strong>');
				toastr.error('Ocurrio un error generando el archivo final.');
			}).always(function () {
				$.CloseLoadingStructData();
			});
		}

		function processNext(index, attempt) {
			if (index >= total) {
				finish();
				return;
			}
			var $row = $rows.eq(index);
			var retrying = false;
			setEstado($row, 'text-info', attempt > 1 ? 'Reintentando...' : 'Procesando...');
			$.ajax({
				type: 'POST',
				url: urlExpediente,
				data: { unidaddocumental_id: $row.data('id'), lote: lote },
				dataType: 'json',
				cache: false
			}).done(function (data) {
				if (data.httpStatus === 200) {
					okCount++;
					var detalle = data.added + ' archivo(s)' + (data.omitted ? ', ' + data.omitted + ' omitido(s)' : '');
					setEstado($row, data.added ? 'text-success' : 'text-warning', data.added ? detalle : 'Sin archivos');
				} else {
					failed++;
					setEstado($row, 'text-danger', data.message);
				}
			}).fail(function () {
				if (attempt < 2) {
					retrying = true;
					processNext(index, attempt + 1);
					return;
				}
				failed++;
				setEstado($row, 'text-danger', 'Error');
			}).always(function () {
				// el reintento ya avanzo por su cuenta
				if (retrying) {
					return;
				}
				processed++;
				updateProgress();
				processNext(index + 1, 1);
			});
		}

		updateProgress();
		processNext(0, 1);
	});
})(jQuery);
