/**
 * Modulo administrativo de cuentas de notificacion por correo (notificacion_cuenta):
 * alterna campos segun el tipo de autenticacion, activa/define principal y ejecuta la prueba del servicio.
 */
(function ($) {
	'use strict';

	function alerta(clase, texto) {
		var $box = $('#nc-mensaje');
		if (!$box.length) {
			return;
		}
		$box.empty().append($('<div class="alert alert-' + clase + ' alert-dismissible"></div>').text(texto));
	}

	function postJson(url, data, done) {
		$.ajax({
			url: url,
			type: 'POST',
			data: data,
			dataType: 'json'
		}).done(done).fail(function (xhr) {
			done({ ok: false, message: 'No fue posible completar la solicitud (HTTP ' + xhr.status + ')' });
		});
	}

	function mostrarTipo() {
		var tipo = $('#nc-tipo').val();
		$('[data-nc-grupo]').each(function () {
			$(this).toggle($(this).data('nc-grupo') === tipo);
		});
	}

	function renderResultado(res) {
		var $out = $('#nc-resultado').empty();
		if (res.message && !res.pasos) {
			$out.append($('<div class="alert alert-danger"></div>').text(res.message));
			return;
		}
		$out.append($('<div class="alert alert-' + (res.ok ? 'success' : 'danger') + '"></div>')
			.text(res.ok ? 'La prueba fue exitosa.' : 'La prueba falló.'));
		var $lista = $('<ul class="list-unstyled nc-pasos"></ul>');
		$.each(res.pasos || [], function (i, paso) {
			var $item = $('<li></li>');
			$item.append($('<i></i>').addClass(paso.ok ? 'entypo-check text-success' : 'entypo-cancel text-danger'));
			$item.append(document.createTextNode(' '));
			$item.append($('<strong></strong>').text(paso.paso));
			$item.append($('<div class="nc-paso-detalle"></div>').text(paso.detalle));
			$lista.append($item);
		});
		$out.append($lista);
		if (res.trazas && res.trazas.length) {
			$out.append($('<a href="#" class="nc-ver-trazas">Ver detalle técnico</a>'));
			$out.append($('<pre class="nc-trazas hidden"></pre>').text(res.trazas.join('\n')));
		}
	}

	$(document).on('change', '#nc-tipo', mostrarTipo);

	$(document).on('click', '.nc-ver-trazas', function (e) {
		e.preventDefault();
		$('.nc-trazas').toggleClass('hidden');
	});

	$(document).on('click', '[data-nc-abrir-prueba]', function (e) {
		e.preventDefault();
		var $panel = $('#nc-panel-prueba');
		$panel.data('id', $(this).data('nc-abrir-prueba')).removeClass('hidden');
		$('#nc-prueba-nombre').text($(this).data('nombre'));
		$('#nc-resultado').empty();
		$('html, body').animate({ scrollTop: $panel.offset().top - 20 }, 200);
	});

	$(document).on('click', '[data-nc-test]', function () {
		var $panel = $('#nc-panel-prueba');
		var tipo = $(this).data('nc-test');
		var data = { id: $panel.data('id') };
		var url = $panel.data('url-conexion');
		if (tipo === 'envio') {
			data.destino = $.trim($('#nc-destino').val());
			url = $panel.data('url-envio');
			if (data.destino === '') {
				$('#nc-resultado').empty().append($('<div class="alert alert-warning"></div>').text('Digite el correo destino de la prueba.'));
				return;
			}
		}
		var $botones = $panel.find('[data-nc-test]').prop('disabled', true);
		$('#nc-resultado').empty().append($('<p></p>').text('Ejecutando prueba...'));
		postJson(url, data, function (res) {
			$botones.prop('disabled', false);
			renderResultado(res);
		});
	});

	function accionLista(selector, atributoUrl, confirmacion) {
		$(document).on('click', selector, function (e) {
			e.preventDefault();
			var id = $(this).data(selector.replace(/^\[data-|\]$/g, ''));
			if (confirmacion && !window.confirm(confirmacion)) {
				return;
			}
			postJson($('#nc-listado').data(atributoUrl), { id: id }, function (res) {
				if (res.ok) {
					window.location.reload();
				} else {
					alerta('danger', res.message);
				}
			});
		});
	}

	accionLista('[data-nc-toggle]', 'url-toggle', null);
	accionLista('[data-nc-principal]', 'url-principal', 'La cuenta pasará a ser la principal. ¿Desea continuar?');

	$(function () {
		mostrarTipo();
	});
}(jQuery));
