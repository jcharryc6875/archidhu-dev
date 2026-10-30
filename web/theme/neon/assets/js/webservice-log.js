// Consulta asincrona del log de servicios web (modulo webservice_log)
jQuery(function ($) {
	var $form = $('#wslog-form');
	if (!$form.length) return;

	var page = 1;
	var pages = 1;
	var $tbody = $('#wslog-tabla tbody');
	var $estado = $('#wslog-estado');

	function esc(v) {
		return $('<div>').text(v === null || v === undefined ? '' : v).html();
	}

	function linkDescarga(id, tipo, texto) {
		var url = $form.data('download-url') + '?id=' + id + '&tipo=' + tipo;
		return '<a class="btn btn-white btn-xs" href="' + url + '">' + texto + '</a>';
	}

	function render(data) {
		var html = '';
		$.each(data.rows, function (i, r) {
			html += '<tr>' +
				'<td class="text-center">' + esc(r.id) + '</td>' +
				'<td>' + esc(r.fecha) + '</td>' +
				'<td>' + esc(r.metodo) + '</td>' +
				'<td>' + esc(r.usuario) + '</td>' +
				'<td class="text-center">' + (r.valido ? 'Sí' : 'No') + '</td>' +
				'<td>' + esc(r.operacion) + '</td>' +
				'<td>' + esc(r.mensaje) + '</td>' +
				'<td class="text-center">' + linkDescarga(r.id, 'request', 'Request') + ' ' + linkDescarga(r.id, 'response', 'Response') + '</td>' +
				'</tr>';
		});
		$tbody.html(html);
		pages = data.pages;
		page = data.page;
		$estado.toggleClass('hidden', data.total > 0).text(data.total > 0 ? '' : 'No existen registros con los filtros indicados.');
		$('#wslog-pagina').text(data.total > 0 ? 'Página ' + page + ' de ' + pages + ' (' + data.total + ' registros)' : '');
		$('#wslog-prev').prop('disabled', page <= 1);
		$('#wslog-next').prop('disabled', page >= pages);
	}

	function cargar(p) {
		$tbody.css('opacity', 0.5);
		$.ajax({
			url: $form.data('list-url'),
			type: 'GET',
			dataType: 'json',
			data: $form.serialize() + '&page=' + p
		}).done(render).fail(function () {
			$estado.removeClass('hidden').text('No se pudo consultar el log. Intente nuevamente.');
		}).always(function () {
			$tbody.css('opacity', 1);
		});
	}

	$form.on('submit', function (e) {
		e.preventDefault();
		cargar(1);
	});
	$('#wslog-prev').on('click', function () { if (page > 1) cargar(page - 1); });
	$('#wslog-next').on('click', function () { if (page < pages) cargar(page + 1); });

	cargar(1);
});
