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
		var paramName = $box.data('param') || 'unidaddocumental_id';
		var unidad = $box.data('unidad') || 'expediente(s)';
		var $rows = $box.find('tr[data-id]');
		var $bar = $box.find('.progress-bar').removeClass('active');
		// segmento animado que muestra el trozo en curso, para que se vea que el sistema trabaja
		var $active = $('<div class="progress-bar progress-bar-info progress-bar-striped active" style="width: 0%"></div>').insertAfter($bar);
		var $aviso = $box.find('.descarga-aviso');
		// el texto dentro de la barra no se ve mientras esta en 0%: el contador queda siempre visible
		var $contador = $('<p class="text-muted descarga-contador"></p>').insertAfter($bar.parent());
		var total = 0;
		var total_rows = $rows.length;
		var done = 0;
		var okCount = 0;
		var failed = 0;

		// cada fila pesa lo que tenga ids (un expediente = 1, un trozo de radicados = N): la barra avanza por unidad
		function countIds(ids) {
			return String(ids).split(',').length;
		}

		$rows.each(function () {
			total += countIds($(this).attr('data-id'));
		});

		function setEstado($row, cls, text) {
			$row.find('.descarga-estado').attr('class', 'descarga-estado text-center ' + cls).text(text);
		}

		function setAviso(type, html) {
			$aviso.attr('class', 'alert alert-' + type + ' descarga-aviso').html(html);
		}

		function updateProgress(inFlight) {
			$bar.css('width', (total ? Math.round((done / total) * 100) : 100) + '%').text(done + ' / ' + total);
			$active.css('width', (total ? Math.round(((inFlight || 0) / total) * 100) : 0) + '%');
			$contador.text(done + ' de ' + total + ' procesado(s)' + (inFlight ? ' - procesando ' + inFlight + ' en este momento' : ''));
		}

		function finish() {
			updateProgress(0);
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
					var msg = 'Archivo generado.' + (failed ? ' ' + failed + ' ' + unidad + ' con error.' : '');
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

		// ids: continuacion de una fila que el servidor no alcanzo a procesar completa en una peticion
		function processNext(index, attempt, ids) {
			if (index >= total_rows) {
				finish();
				return;
			}
			var $row = $rows.eq(index);
			var retrying = false;
			var pending = '';
			var sent = ids || $row.attr('data-id');
			var params = { lote: lote };
			params[paramName] = sent;
			var started = Date.now();
			function label() {
				var added = $row.data('added') || 0;
				return (attempt > 1 ? 'Reintentando...' : 'Procesando...') + (added ? ' ' + added + ' archivo(s),' : '') +
					' ' + Math.floor((Date.now() - started) / 1000) + ' s';
			}
			setEstado($row, 'text-info', label());
			updateProgress(countIds(sent));
			// el contador de segundos demuestra que la peticion sigue en curso
			var timer = setInterval(function () {
				setEstado($row, 'text-info', label());
			}, 1000);
			$.ajax({
				type: 'POST',
				url: urlExpediente,
				data: params,
				dataType: 'json',
				cache: false
			}).done(function (data) {
				if (data.httpStatus === 200) {
					var added = ($row.data('added') || 0) + data.added;
					var omitted = ($row.data('omitted') || 0) + data.omitted;
					$row.data('added', added).data('omitted', omitted);
					var detalle = added + ' archivo(s)' + (omitted ? ', ' + omitted + ' omitido(s)' : '');
					done += countIds(sent) - (data.pending ? countIds(data.pending) : 0);
					if (data.pending) {
						pending = data.pending;
					} else {
						okCount++;
						setEstado($row, added ? 'text-success' : 'text-warning', added ? detalle : 'Sin archivos');
					}
				} else {
					failed++;
					done += countIds(sent);
					setEstado($row, 'text-danger', data.message);
				}
			}).fail(function () {
				if (attempt < 2) {
					retrying = true;
					processNext(index, attempt + 1, ids);
					return;
				}
				failed++;
				done += countIds(sent);
				setEstado($row, 'text-danger', 'Error');
			}).always(function () {
				clearInterval(timer);
				// el reintento ya avanzo por su cuenta
				if (retrying) {
					return;
				}
				updateProgress(0);
				if (pending) {
					processNext(index, 1, pending);
					return;
				}
				processNext(index + 1, 1);
			});
		}

		updateProgress(0);
		processNext(0, 1);
	});
})(jQuery);
