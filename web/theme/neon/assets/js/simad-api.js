/* SIMAD API - Hugo Fernando Castillo / Javier Fernando Charry */
jQuery(document).ready(function ($) {
	var timer;
	// Funcion para campos readonly
	jQuery('.data-readonly').keydown(function (e) {
		if (e.keyCode === 8 || e.keyCode === 46)  // Backspace & del
			e.preventDefault();
	}).on('keypress paste cut', function (e) {
		e.preventDefault();
	});

	// UARIV-202605 (ampliación): pone en modo solo lectura los CKEditor cuyo textarea
	// original traiga data-ckeditor-readonly="1" (candado de edición por etapa/documento).
	if (typeof CKEDITOR !== 'undefined') {
		CKEDITOR.on('instanceReady', function (evt) {
			if (jQuery('#' + evt.editor.name).data('ckeditor-readonly')) {
				evt.editor.setReadOnly(true);
			}
		});
	}

	// Manejar el evento cancel para limpiar la selección
	jQuery(document).on('click', '.daterangepicker .cancelBtn', function (event) {
		jQuery('#erecfecha_inicio').val('');
		jQuery('#erecfecha_fin').val('');
		jQuery(".daterange-inline").find('span').html("");
	});

	jQuery(document).on('click', '.linktocom', function (event) {
		event.preventDefault();

		$.LoadingStructData();

		jQuery.ajax({
			method: 'POST',
			url: '/enviada.php/com_enviada/updateComrecibidaRespuesta',
			data: jQuery.param({ comenviada_id: jQuery(this).data('comenviada_id'), comrecibida_id: jQuery(this).data('comrecibida_id') }),
			success: function (response) {
				if (response.status == 200) {
					toastr.success(response.mensaje);
					toastr.info("La informacion se actualizara en un momento, espere por favor");
					setTimeout(function () { parent.jQuery.CloseModalSIMAD(); }, 5000)
				}
				else if (response.status == 300) {
					$.CloseLoadingStructData();
					toastr.warning(response.mensaje);
				}
				else if (response.status == 400) {
					$.CloseLoadingStructData();
					toastr.error(response.mensaje);
				}
				else {
					$.CloseLoadingStructData();
					toastr.error("Error Interno del Servidor!");
				}

			},
			error: function (jqXHR, textStatus, errorThrown) {
				if (jqXHR.status == 404) {
					$.CloseLoadingStructData();
					toastr.error("Error Interno del Servidor!");
				}
			}
		});
	});

	jQuery(document).on('change', '#subserie_id', function () {
		var proccess_call = jQuery(this).data('endpro');
		var fields_position = 'left';
		var endpoint = '/archivo.php/subserie/loadMetadatos';

		if (proccess_call == "search")
			var endpoint = '/archivo.php/subserie/searchMetadatos';

		if (jQuery('#row_position').length > 0) {
			fields_position = jQuery('#row_position').val();
		}

		$.ajax(
			{
				type: 'POST',
				url: endpoint,
				data: jQuery.param({ subserie_id: $(this).val(), row_position: fields_position }),
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				cache: false,
				processData: false,
				beforeSend: function () { jQuery(".dinamicallfields").remove(); },
				success: function (data) {
					try {
						if (data == null || data == '') {
							jQuery(".dinamicallfields").remove();
						}
						else {
							$("#structmetadatos").after(data);
							applyDatePicker()
						}
					}
					catch (err) {
					}
				},
				complete: function () {
				},
			});
	});

	jQuery('.advinfosetting').change(function (e) {
		e.preventDefault();
		$.ajax({
			url: '/servicios.php/servicio/advancedFields',
			method: 'POST',
			data: jQuery.param({ tiposervicio_id: jQuery(this).val(), servicio_id: jQuery("#servicio_id").val(), icompk_id: jQuery("#com_id").val(), modulo_id: jQuery("#modulo_id").val() }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			error: function (response) {
				//$.toastr.info("Error, no se pudo cargar la informacion solicitada!"); 
			},
			success: function (response) {
				// set content response to div
				if (response.length === 0) {
					$(".ndinamicfields").remove();
				} else {
					//$(".newfields").html(response);
					$(".newfields").after(response);
					$.applyDatePicker();
				}
			}
		});
	});

	$.applyTileStyle = function () {
		$(".scrollcms").each(function (i, el) {
			var $this = $(el);
			var el_height = $this.attr("data-height");
			$this.css('height', el_height);
			$this.css('overflow', 'visible');
			$this.css('width', 'auto');
		});
	}

	jQuery('.advinfocomentcustom').change(function (e) {
		e.preventDefault();
		$.ajax({
			url: '/enviada.php/com_enviada/advancedFields',
			method: 'POST',
			data: jQuery.param({ plantillascom_id: jQuery(this).val(), comenviada_id: jQuery("#comenviada_id").val() }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			error: function (response) {
				//$.toastr.info("Error, no se pudo cargar la informacion solicitada!"); 
			},
			success: function (response) {
				// set content response to div
				if (response.length === 0) {
					$(".newfields").hide();
					$(".newfields").empty();
				} else {
					$(".newfields").append(response);
					$(".newfields").show();
					//$(".newfields").after(response);
					$.applyDatePicker();
				}
			}
		});
	});

	jQuery('body').on('blur', '.vinlinescom', function (event) {
		event.stopPropagation();
		$.processResByInt();
	});

	jQuery('body').on('change', '.vinlinescomint', function (event) {
		event.stopPropagation();
		$.processResByInt();
	});

	$.processResByInt = function () {
		if (jQuery(".newfields").length) {
			if (jQuery('#numero_resolucion').val().length != 0 && jQuery('#idUserInteresados').val().length != 0) {
				jQuery.ajax({
					method: "POST",
					url: '/enviada.php/com_enviada/advFieldsComValid',
					data: jQuery.param({ comenviada_id: jQuery("#comenviada_id").val(), numresolucion: jQuery('#numero_resolucion').val(), idUserInteresados: jQuery('#idUserInteresados').val() }),
					success: function (response) {
						if (response.status == 'error') {
							jQuery('#numero_resolucion').val("");
							var $validator = $(".validate").validate();
							errors = { numero_resolucion: "Ya existe un radicado con el n&uacute;mero de resoluci&oacute;n para el interesado" };
							$validator.showErrors(errors);
						}
					}
				});
			}
		}
	}

	jQuery('.processrejectenv.dropdown').on("show.bs.dropdown", function (event) {
		event.stopPropagation();
		jQuery.ajax({
			method: "POST",
			url: '/enviada.php/com_enviada/rejectedLinksOpt',
			data: jQuery.param({ comenviada_id: jQuery('#comenviada_id').val() }),
			beforeSend: function () {
				$('.dropdown-menu').html('');
				$.LoadingStructData();
			},
			error: function (response) {
				$('.dropdown-menu').html('');
				$.CloseLoadingStructData();
			},
			success: function (response) {
				$('.dropdown-menu').html(response);
				$.CloseLoadingStructData();
			}
		});
	});

	jQuery(document).on('switch-change', '.make-switch.exclusive-group', function (event, container) {
		var $currentSwitch = jQuery(this);
		var element_current = jQuery(this).data('idcurrent');
		var erefresh = jQuery(this).data('erefresh');

		event.stopPropagation();
		// Prevenir bucle infinito
		if ($currentSwitch.data('updating')) {
			$currentSwitch.data('updating', false);
			return;
		}


		if (container.value === true) {
			// Marcar todos como actualizando
			jQuery('[data-exclusive-group="grpcom-unique"]').data('updating', true);

			// Desactivar los otros switches
			jQuery('[data-exclusive-group="grpcom-unique"]').not($currentSwitch).each(function (index, item) {
				var $otherSwitch = jQuery(item);
				$otherSwitch.bootstrapSwitch('setState', false);
			});

			var endpoint = jQuery(this).data('endpoint');
			jQuery.ajax({
				method: "POST",
				url: endpoint,
				data: jQuery.param({ chekedcom: container.value }),
				beforeSend: function () {
					$.LoadingStructData();
				},
				error: function (response) {
				},
				success: function (response) {
					try {
						jQuery(".tmpresponse").hide();
						jQuery("#" + erefresh).html(response).show();

					} catch (err) {
						toastr.error(err.message);
					}
				},
				complete: function () {
					$.CloseLoadingStructData();
				}
			});

			// Remover flag después de un delay
			setTimeout(function () {
				jQuery('[data-exclusive-group="grpcom-unique"]').data('updating', false);
			}, 50);
		} else {
			jQuery(".tmpresponse").show();
			jQuery("#tplcomrad").hide();
		}
	});

	jQuery(document).on('switch-change', '.make-switch.tplcom-options', function (event, data) {
		var $element = data.el;
		var newValue = data.value;
		var pkobject_id = jQuery($element).data('pkobject');

		// Evitar bucles si se está actualizando
		if (jQuery(this).data('updating')) return;

		// Marcar como "actualizando"
		jQuery(this).data('updating', true);

		event.stopPropagation();

		jQuery.ajax({
			method: "POST",
			url: '/administracion.php/plantillas_com/enableComDoc',
			data: jQuery.param({ objectcom_pk: pkobject_id, isEnable: newValue }),
			beforeSend: function () {
			},
			error: function (response) {
				var $input = jQuery('input[data-pkobject="' + pkobject_id + '"]');
				var $switchContainer = $input.closest('.make-switch');
				$switchContainer.bootstrapSwitch('setState', !newValue);
				toastr.error("Ocurrio un error en el servidor, no se pudo completar la solicitud");
			},
			success: function (response) {
			},
			complete: function () {
				// Quitar marca de actualización
				jQuery(event.target).removeData('updating');
			}
		});
	});

	jQuery('.processreject.dropdown').on("show.bs.dropdown", function (event) {
		event.stopPropagation();
		jQuery.ajax({
			method: "POST",
			url: '/interna.php/com_interna/rejectedLinksOpt',
			data: jQuery.param({ cominterna_id: jQuery('#cominterna_id').val() }),
			beforeSend: function () {
				$('.dropdown-menu').html('');
				$.LoadingStructData();
			},
			error: function (response) {
				$('.dropdown-menu').html('');
				$.CloseLoadingStructData();
			},
			success: function (response) {
				$('.dropdown-menu').html(response);
				$.CloseLoadingStructData();
			}
		});
	});

	jQuery('#feeduncheckradcom').on('click', function (event) {
		event.stopPropagation();
		try {
			jQuery('.strcomin').each(function (index, item) {
				if (parseInt(jQuery(item).data('index')) <= 0) {
					jQuery.ajax({
						method: "POST",
						url: '/enviada.php/com_enviada/updateRadicarBatch',
						data: jQuery.param({ comenviada_id: jQuery(item).val() }),
						beforeSend: function () {
							$.LoadingStructData();
						},
						error: function (response) {
							$.CloseLoadingStructData();
							toastr.error("Ocurrio un error en el servidor, no se pudo completar la solicitud");
							jQuery('[data-toggle="tooltip"]').tooltip();
						},
						success: function (response) {
							$.CloseLoadingStructData();

							if (response.isError == true) {
								jQuery('#' + response.statusel).html('<img class="tooltip-primary" data-toggle="tooltip" data-original-title="Error al radicar, ' + response.message + '" width="25" height="25" src="/images/simad/bullet_red.png"/>');
								jQuery('#' + response.radcomel).text(response.radicado);
								jQuery('#' + response.fcreatecomel).text(response.fecha_creacion);
							} else {
								jQuery('#' + response.statusel).html('<img class="tooltip-primary" data-toggle="tooltip" data-original-title="Radicado exitoso" width="25" height="25" src="/images/simad/bullet_green.png"/>');
								jQuery('#' + response.radcomel).text(response.radicado);
								jQuery('#' + response.fcreatecomel).text(response.fecha_creacion);
							}
							jQuery('[data-toggle="tooltip"]').tooltip();
						},
					});
				}
			});
		} catch (error) {
			$.CloseLoadingStructData();
			toastr.error("Ocurrio un error en el servidor, no se pudo completar la solicitud");
		}
	});


	jQuery('#saveActoAdm').on('click', function (event) {
		event.stopPropagation();
		try {
			if (jQuery(this).closest('form').valid()) {
				let buttonValue = $(this).val();
				$.LoadingStructData();
				$.ajax({
					url: '/comun.php/acto_administrativo/update',
					method: 'POST',
					data: jQuery(this.form.elements).serialize() + '&optadd=' + buttonValue,
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					success: function (response) {
						if (response.status == 200) {
							$.CloseLoadingStructData();
							toastr.success(response.message);
							window.location.href = response.url_redirect;
						} else {
							$.CloseLoadingStructData();
							toastr.error(response.message);
						}
					},
					error: function (response) {
						$.CloseLoadingStructData();
						toastr.error(response.message);
					}
				});
			}
		} catch (error) {
			$.CloseLoadingStructData();
			toastr.error("Ocurrio un error en el servidor, no se pudo completar la solicitud," + error);
		}
	});

	jQuery('.processrejectact.dropdown').on("show.bs.dropdown", function (event) {
		event.stopPropagation();
		jQuery.ajax({
			method: "POST",
			url: '/comun.php/acto_administrativo/rejectedLinksOpt',
			data: jQuery.param({ actoadministrativo_id: jQuery('#actoadministrativo_id').val() }),
			beforeSend: function () {
				$('.dropdown-menu').html('');
				$.LoadingStructData();
			},
			error: function (response) {
				$('.dropdown-menu').html('');
				$.CloseLoadingStructData();
			},
			success: function (response) {
				$('.dropdown-menu').html(response);
				$.CloseLoadingStructData();
			}
		});
	});

	jQuery('#directorioexterno_id').change(function (e) {
		e.preventDefault();
		var $vfield = jQuery(this).val().trim();
		if ($vfield.length != 0) {
			jQuery('.requiredxor').addClass("required");
		} else {
			jQuery('.requiredxor').removeClass("required");
			$.each($('.form-control.requiredxor'), function (index, value) {
				$vitem = jQuery('#' + $(value).attr('name') + '-error');
				if ($vitem.length) {
					$vitem.remove();
				}
			});
		}
	});

	jQuery('.tplselectcom').change(function (e) {
		e.preventDefault();
		var vfield = jQuery(this);
		if (vfield.length != 0) {
			var vitem = jQuery(this).val().trim();
			if (vitem) {
				if (vitem == 1) {
					jQuery('.xtcomvalid').removeClass("required");
				} else {
					jQuery('.xtcomvalid').addClass("required");
				}
			}
		}
	});

	jQuery('.tramitebyarea').change(function (e) {
		e.preventDefault();

		var valorSeleccionado = parseInt(jQuery(this).val());
		$element = jQuery('#tipo_com_recibida_id');
		$element.empty();

		jQuery.ajax({
			url: '/recibida.php/com_recibida/loadAreaByTramite',
			method: 'POST',
			data: jQuery.param({ dependencia_id: jQuery(this).val() }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			error: function (response) {
				$element.val(null).trigger('change');
				var option = new Option('Seleccione...', '', true, true);
				$element.append(option).trigger('change');
			},
			success: function (response) {
				var option = new Option('Seleccione...', '', true, true);
				jQuery('#tipo_com_recibida_id').append(option);

				for (var idx = 0; idx < response.length; idx++) {
					var option = new Option(response[idx].text, response[idx].id, false, false);
					jQuery('#tipo_com_recibida_id').append(option);
				}

				$element.trigger('change');
				var valorActivador = jQuery('#div_regional_destino').data('valoractivador');

				if (valorSeleccionado === valorActivador) {
					jQuery.ajax(
						{
							type: 'POST',
							url: '/recibida.php/com_recibida/regionalesDest',
							contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
							cache: false,
							processData: false,
							success: function (response) {
								if (response.status == 200) {
									var nuevoDiv = response.text_select;
									jQuery('#div_regional_destino').html(nuevoDiv);
									jQuery('#div_regional_destino').show();
									$.highlightElement(jQuery('#regional_destino_id'));
								}
								else if (response.status == 400) {
									jQuery('#div_regional_destino').hide();
								}
								else {
									jQuery('#div_regional_destino').hide();
								}
							},
							error: function (response) {
								jQuery('#div_regional_destino').hide();
							},
							complete: function () {
							},
						});
				}
				else {
					jQuery('#div_regional_destino').html('');
					jQuery('#div_regional_destino').hide();
				}
			}
		});
	});

	$.applyDatePicker = function () {
		$(".datepicker").each(function (i, el) {
			var $this = $(el),
				opts = {
					format: attrDefault($this, 'format', 'mm/dd/yyyy'),
					startDate: attrDefault($this, 'startDate', ''),
					endDate: attrDefault($this, 'endDate', ''),
					daysOfWeekDisabled: attrDefault($this, 'disabledDays', ''),
					startView: attrDefault($this, 'startView', 0),
					autoclose: true,
					language: 'es',
					rtl: rtl()
				},
				$n = $this.next(),
				$p = $this.prev();

			$this.datepicker(opts);

			if ($n.is('.input-group-addon') && $n.has('a')) {
				$n.on('click', function (ev) {
					ev.preventDefault();

					$this.datepicker('show');
				});
			}

			if ($p.is('.input-group-addon') && $p.has('a')) {
				$p.on('click', function (ev) {
					ev.preventDefault();

					$this.datepicker('show');
				});
			}
		});
	}

	$.highlightSelect2 = function ($select, effect = 'highlight', duration = 1200) {
		if (!$select.data('select2') || !$select.data('select2').container) return;

		var $container = $select.data('select2').container;
		var className = 'select2-' + effect;

		$container.addClass(className);
		setTimeout(() => $container.removeClass(className), duration);
	}

	$.highlightElement = function ($element, effect = 'highlight', duration = 1200) {
		if (!$element || $element.length === 0) return;

		// Si es un Select2, aplicar al contenedor
		if ($element.data('select2') && $element.data('select2').container) {
			var $container = $element.data('select2').container;
			var className = 'select2-' + effect;
			$container.addClass(className);
			setTimeout(() => $container.removeClass(className), duration);
		} else {
			var className = 'highlight-' + effect;
			$element.addClass(className);
			setTimeout(() => $element.removeClass(className), duration);
		}
	}

	jQuery.validator.addMethod("allowed-domain", function (value, element) {
		if (!value) return true;
		const domain = value.split('@')[1];
		return allowedDomains.includes(domain);
	}, "Este dominio de correo no está permitido.");

	jQuery.validator.addMethod("folder-treename", function (value, element) {
		if (!value) return true;
		const regex = /^[A-Za-z0-9][A-Za-z0-9_\- ]{3,100}[A-Za-z0-9]$/;
		return this.optional(element) || regex.test(value);
	}, "El nombre debe tener entre 5 y 100 caracteres, iniciar y terminar con letra o número, y solo puede contener letras, números, guion bajo (_), guion medio (-) o espacios en los caracteres intermedios.");

	jQuery.validator.addMethod("corerule", function (value, element) {
		var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&#])([A-Za-z\d$@$!%*?&#]|[^ ]){8,25}$/;
		return this.optional(element) || regex.test(value);
	}, "Las contrase&ntilde;as deben tener minimo 8 caracteres y maximo 25, deben incluir may&uacute;sculas , min&uacute;sculas, por lo menos un n&uacute;mero y minimo un caracter especial($@$!%*?&#)");

	jQuery.validator.addMethod("loginrule", function (value, element) {
		var regex = /^[a-zA-Z0-9]{2,}[\.]{0,1}[a-zA-Z0-9]{2,}$/i;
		return this.optional(element) || regex.test(value);
	}, "Los nombres de usuario solo deben estar compuestos por numeros y/o letras separados por un punto");

	jQuery.validator.addMethod("requiredIfOtherEmptySelect2", function (value, element, param) {
		var otherValue;

		// Si el otro elemento es un select2
		if ($(param).hasClass("select2-hidden-accessible")) {
			otherValue = $(param).val();
			return ($.trim(value) !== "") || ($.trim(otherValue) !== "");
		} else {
			otherValue = $(param).val();
			return ($.trim(value) !== "") || ($.trim(otherValue) !== "");
		}
	}, "Por favor complete al menos {0} de estos campos.");

	jQuery.validator.addMethod("fillone", function (value, element, options) {
		var $fields = jQuery(options[1], element.form),
			$fieldsFirst = $fields.eq(0),
			validator = $fieldsFirst.data('valid_req_grp') ? $fieldsFirst.data('valid_req_grp') : jQuery.extend({}, this),
			isValid = $fields.filter(function () {
				return validator.elementValue(this);
			}).length >= options[0];

		// Store the cloned validator for future validation
		$fieldsFirst.data('valid_req_grp', validator);
		// If element isn't being validated, run each require_from_group field's validation rules
		if (!jQuery(element).data('being_validated')) {
			$fields.data('being_validated', true);
			$fields.each(function () {
				validator.element(this);
			});
			$fields.data('being_validated', false);
		}
		return isValid;
	}, "Por favor complete al menos {0} de estos campos.");

	jQuery('body').on('change', '.requiredXorEmptySelect2', function (event) {
		let current_object = jQuery(this);
		let value_element = current_object.val();
		let empty_select = false;

		if (value_element === null || value_element === '' || value_element.length === 0) {
			empty_select = true;
		}

		// Validar si algunos de los elementos requeridos estan diligenciados
		let filledCount = 0;
		jQuery(".requiredXorEmptySelect2").each(function (i, row) {
			if (jQuery(row).val() !== null && jQuery(row).val() !== '') {
				filledCount++;
			}
		});

		jQuery(".requiredXorEmptySelect2").each(function (i, el) {
			if (jQuery(el).attr('name') !== undefined) {
				if (empty_select && filledCount === 0) {
					jQuery(el).addClass("required");
				} else if (current_object.attr('id') !== jQuery(el).attr('id')) {
					jQuery(el).removeClass("required");
				}
			}
		});

		jQuery(this).closest('form').valid();
	});

	var isUpdating = false;
	jQuery('body').on('change', '.requiredXorMaxOneSelect2', function (event) {
		if (isUpdating) return; // prevenir recursión

		const current_object = jQuery(this);
		const current_id = current_object.attr('id');
		//const currentValue = current_object.val();

		isUpdating = true;

		jQuery(".requiredXorMaxOneSelect2").each(function (i, row) {
			const element_item = jQuery(row);
			const element_id = element_item.attr('id');

			if (element_id !== current_id) {
				if (element_item.val() && element_item.val() !== '')
					jQuery(element_item).select2('val', '').trigger('change');
			}
		});

		// Validar si ambos están vacíos
		let filledCount = 0;
		jQuery(".requiredXorMaxOneSelect2").each(function (i, row) {
			if (jQuery(row).val() !== null && jQuery(row).val() !== '') {
				filledCount++;
			}
		});

		// Aplicar clase 'required' si ambos vacíos
		jQuery(".requiredXorMaxOneSelect2").each(function (i, row) {
			const element_item = jQuery(row);
			if (filledCount === 0) {
				element_item.addClass('required');
			} else {
				element_item.removeClass('required');
			}
		});

		isUpdating = false;
	});

	var $lastOpener = $();
	$.isFocusable = function ($el) {
		return $el && $el.length &&
			$el.is(':visible') &&
			!$el.is(':disabled') &&
			$el.is('a[href], button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
	}

	$.restoreFocus = function () {
		if (!$lastOpener || !$lastOpener.length) return;
		setTimeout(function () {
			if ($.isFocusable($lastOpener)) {
				$lastOpener.focus();
			} else {
				var $fallback = $lastOpener.closest('form, body').find('a,button,input,select,textarea,[tabindex]:not([tabindex="-1"])').filter(':visible').first();
				if ($fallback.length) $fallback.focus();
			}
			$lastOpener = $();
		}, 0);
	}

	// Funcion para abrir Modal Fancybox
	// Parametros URL, Ancho, Alto
	$.OpenModalSIMAD = function (url, ancho, alto) {
		$lastOpener = $(document.activeElement);
		if (typeof (ancho) === undefined) ancho = 800;
		if (typeof (alto) === undefined) alto = 600;

		$.fancybox.open({
			href: url,
			type: 'iframe',
			maxWidth: ancho,
			maxHeight: alto,
			fitToView: false,
			width: '99%',
			height: '99%',
			autoSize: false,
			closeClick: false,
			openEffect: 'none',
			closeEffect: 'none',
			helpers: {
				overlay: { closeClick: false } // prevents closing when clicking OUTSIDE fancybox
			},
			keys: {
				// prevents closing when press ESC button
				close: null
			},
			afterClose: function () {
				//parent.location.reload(true); 
			},
			beforeShow: function () {
				setTimeout(function () {
					var $iframe = $('.fancybox-iframe');
					try {
						// Inyectar CSS en el iframe
						var $head = $iframe.contents().find('head');
						$head.append('<style>' +
							'input:focus, textarea:focus, select:focus {' +
							'border: 1px solid #007bff !important;' +
							'color: #333 !important;' +
							'outline: none !important;' +
							'box-shadow: 0 0 5px rgba(0, 123, 255, 0.5) !important;' +
							'}' +
							'</style>');
					} catch (e) {
						//console.log('No se puede modificar el CSS del iframe (cross-domain)');
					}
				}, 500);
			},
			afterShow: function () {
				const firstInput = $(".fancybox-iframe").contents().find('input.form-control.input-sm:not([type="hidden"]):not([type="submit"]):not([type="button"]), select:not([id="serie_id"]):not([id="subserie_id"])').filter(':visible').first();
				setTimeout(function () {
					firstInput.focus();
				}, 200);
			},
			afterClose: function () {
				$.restoreFocus();
			}
		});
	};

	$.setTplComContents = function (contents) {
		if (CKEDITOR.instances.contenido.getData() == '') {
			CKEDITOR.instances.contenido.setData(contents);
		} else if (confirm("Si continua se remplazara el contenido de la comunicacion") == true) {
			CKEDITOR.instances.contenido.setData(contents);
		}
	};

	$.setInlineErrorMig = function (data, modal_name, aelement) {
		try {
			var response = JSON.parse(data);
			if (response.status == 200) {
				$(".next").removeClass("disabled");
				toastr.success('Todos los datos son validos, haga clic en el boton siguiente para continuar');
				$("#comIdLote").val(response.comIdLote);
				aelement.remove();
			} else {
				$('#' + modal_name + ' .modal-body').html('');
				var items = '';
				$.each(response.message, function (i, item) {
					items += '<li style="word-wrap: break-word;"><strong>' + item + '</strong></li>';
				});
				$('#' + modal_name + ' .modal-body').append("<ul>" + items + "</ul>");
				$.fancybox.open({
					href: '#' + modal_name,
					type: 'inline',
					fitToView: false,
					width: '50%',
					height: '50%',
					autoSize: false,
					padding: 0,
					closeBtn: false,

					helpers: {
						overlay: { closeClick: false }
					}
				});
			}
		} catch (err) {
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	$.batchMigResult = function (data, exitosos, errores) {
		try {
			var response = JSON.parse(data);
			if (response.status == 200) {
				toastr.success(response.message);

				jQuery.ajax({
					url: '/enviada.php/com_enviada/loadListBatchMig',
					method: 'POST',
					data: jQuery.param({ comIdLote: jQuery("#comIdLote").val() }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					error: function (response) {
						toastr.error("Opps, ocurrio un error al cargar la informacion del lote de radicacion masiva, por favor verique la informacion en el modulo de enviadas!");
					},
					success: function (response) {
						$("#resultmigdata").html(response);
						$.mostrarResultadoFinal(exitosos, errores);
						$("#resultmigdata").show();
						$(".next").removeAttr('disabled').trigger('click');
					}
				});

			} else {
				toastr.error(response.message);
				$(".next").addClass("disabled");
			}
		} catch (err) {
			$(".next").addClass("disabled");
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	$.batchMigResultAsyncActoAdm = function (data, exitosos, errores) {
		try {
			var response = JSON.parse(data);
			if (response.status == 200) {
				toastr.success(response.message);

				jQuery.ajax({
					url: '/comun.php/acto_administrativo/loadListBatchMig',
					method: 'POST',
					data: jQuery.param({ comIdLote: jQuery("#comIdLote").val() }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					error: function (response) {
						toastr.error("Opps, ocurrio un error al cargar la informacion del lote de radicacion masiva, por favor verifique la informacion en el modulo de actos administrativos!");
					},
					success: function (response) {
						$("#resultmigdata").html(response);
						$.mostrarResultadoFinal(exitosos, errores);
						$("#resultmigdata").show();
						$(".next").removeAttr('disabled').trigger('click');
					}
				});

			} else {
				toastr.error(response.message);
				$(".next").addClass("disabled");
			}
		} catch (err) {
			$(".next").addClass("disabled");
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	$.batchMigResultActoAdm = function (data) {
		try {
			var response = JSON.parse(data);
			if (response.status != 200) {

				if (response.status == 200) {
					toastr.success(response.message);
				} else {
					toastr.warning(response.message);
				}

				jQuery.ajax({
					url: '/comun.php/acto_administrativo/loadListBatchMig',
					method: 'POST',
					data: jQuery.param({ comIdLote: jQuery("#comIdLote").val() }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					error: function (response) {
						toastr.error("Opps, ocurrio un error al cargar la informacion del lote de radicacion masiva, por favor verique la informacion en el modulo de enviadas!");
					},
					success: function (response) {
						$("#resultmigdata").html(response);
						$("#resultmigdata").show();
						$(".next").removeAttr('disabled').trigger('click');
					}
				});

			} else {
				toastr.error(response.message);
				$(".next").addClass("disabled");
			}
		} catch (err) {
			$(".next").addClass("disabled");
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	/**
	 * $.initListaDatosRadMasivaTable()
	 * Inicializa el datatable de la "Lista Datos" de radicacion masiva (Actos Administrativos,
	 * Enviadas, Recibidas, Internas) con soporte responsive y filtro por columna.
	 * @param tableSelector string selector jQuery de la tabla (ej. '#table-1')
	 */
	$.initListaDatosRadMasivaTable = function (tableSelector) {
		var tableContainer = $(tableSelector);
		if (tableContainer.length === 0) { return; }

		var responsiveHelper;
		var breakpointDefinition = { tablet: 1024, phone: 480 };

		tableContainer.dataTable({
			"sPaginationType": "bootstrap",
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"bStateSave": true,
			"language": {
				"lengthMenu": "Mostrando _MENU_ registros por pagina",
				"zeroRecords": "Lo sentimos, Ningun registro encontrado",
				"info": "Mostrado _START_ a _END_ de _TOTAL_ registros",
				"infoEmpty": "Ningun registro encontrado",
				"search": "Buscar:",
				"infoFiltered": "(Registros filtrados de un total de _MAX_ registros)"
			},
			bAutoWidth: false,
			fnPreDrawCallback: function () {
				if (!responsiveHelper) {
					responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
				}
			},
			fnRowCallback: function (nRow) {
				responsiveHelper.createExpandIcon(nRow);
			},
			fnDrawCallback: function () {
				responsiveHelper.respond();
			}
		});

		tableContainer.columnFilter({ "sPlaceHolder": "head:after" });

		$(".dataTables_wrapper select").select2({ minimumResultsForSearch: -1 });

		$('[data-toggle="tooltip"]').tooltip();
	};

	$.batchMigResultRec = function (data) {
		try {
			var response = JSON.parse(data);
			if (response.status == 200) {
				toastr.success(response.message);

				jQuery.ajax({
					url: '/recibida.php/com_recibida/loadListBatchMig',
					method: 'POST',
					data: jQuery.param({ comIdLote: jQuery("#comIdLote").val() }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					error: function (response) {
						toastr.error("Opps, ocurrio un error al cargar la informacion del lote de radicacion masiva, por favor verique la informacion en el modulo de enviadas!");
					},
					success: function (response) {
						$("#resultmigdata").html(response);
						$("#resultmigdata").show();
						$(".next").removeAttr('disabled').trigger('click');
					}
				});

			} else {
				toastr.error(response.message);
				$(".next").addClass("disabled");
			}
		} catch (err) {
			$(".next").addClass("disabled");
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	$.batchMigResultTRD = function (data) {
		try {
			var response = JSON.parse(data);
			if (response.status == 200) {
				toastr.success(response.message);
				$("#resultmigdata").html(response);
				$("#resultmigdata").show();
				$(".next").removeAttr('disabled').trigger('click');
				$(".next").removeAttr('disabled').trigger('click');
			} else {
				toastr.error(response.message);
				$(".next").addClass("disabled");
			}
		} catch (err) {
			$(".next").addClass("disabled");
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	$.CustomConfirmEvent = async function (element) {
		try {
			let controller = $(element).data("endpoint");
			let method = $(element).data("method-type");
			let submit_time = $(element).data("submit_time");
			let customMessage = jQuery(this).data('message') || 'Sin embargo, puedes cancelarla si no estas seguro, tienes ' + submit_time + ' segundos.';

			const confirmado = await $.mostrarConfirmacion(submit_time, customMessage);

			if (!confirmado) {
				toastr.success('Usuario canceló la operaciòn');
				return;
			}

			if (method === "href") {
				$.LoadingStructData();
				window.location.href = controller;
			}
			else {
				// Ejecutar el AJAX original
				return $.ajax({
					url: controller,
					method: 'POST',
					data: $(element).closest('form').serialize(),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					beforeSend: function () {
						$.LoadingStructData();
					},
					success: function (response) {
						try {
							if (response.status == 200) {
								toastr.success(response.message);
								setTimeout(function () {
									document.location.reload();
								}, 3000);
							} else {
								toastr.error(response.message);
							}
						} catch (err) {
							$.CloseLoadingStructData(); toastr.error(err.message);
						}
					},
					error: function (response) {
						$.CloseLoadingStructData();
						toastr.error("Error Interno del Servidor!");
					}
				});
			}


		} catch (error) {
			toastr.success('Error Interno del Servidor! canceló la operaciòn');
		}
	}

	$.batchMigResultTRD = function (data) {
		try {
			var response = JSON.parse(data);
			if (response.status == 200) {
				toastr.success(response.message);
				$("#resultmigdata").html(response);
				$("#resultmigdata").show();
				$(".next").removeAttr('disabled').trigger('click');
				$(".next").removeAttr('disabled').trigger('click');
			} else {
				toastr.error(response.message);
				$(".next").addClass("disabled");
			}
		} catch (err) {
			$(".next").addClass("disabled");
			$.CloseLoadingStructData();
			toastr.error(err.message);
		}
	};

	$.ExportReportSave = function (element, jdata = null) {
		return $.ajax({
			type: 'POST',
			url: '/backend.php/busqueda_avanzada/formGenerarReporte',
			data: jQuery(element.closest('form')).serialize(), //serializar.
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			beforeSend: function () {
				$.LoadingStructData();
				$('#rowfieldsave').hide();
				$('#rowfieldsave').html('');
			},
			success: function (data) {
				$.CloseLoadingStructData();
				try {
					$('#rowfieldsave').append(data);

					if (jdata != null) {
						$('#rowfieldsave').find("#nombre_reporte").val(jdata.reporte_nombre);
						$('#rowfieldsave').find("#descripcion_reporte").val(jdata.reporte_descripcion);
					}

					$('#rowfieldsave').show();
				}
				catch (err) {
					toastr.error(err.message);
					$('#rowfieldsave').hide();
					$('#rowfieldsave').html('');
				}
			},
			error: function (response) {
				toastr.error('Debe seleccionar un campo para el reporte de la lista');
				$('#rowfieldsave').hide();
				$('#rowfieldsave').html('');
				$.CloseLoadingStructData();
			},
			complete: function () {
				$.CloseLoadingStructData();
			},
		}).always($.CloseLoadingStructData);
	};

	$.loadTooltipData = function ($el, contentUrl) {
		$.ajax(contentUrl, {
			method: 'POST',
			data: jQuery.param({ qsource: $el.data("qsource"), qthumb: $el.data("qthumb") }),
			beforeSend: function () {
				$(".popover").remove();
			},
			success: function (data) {
				$el.data('bs.popover').options.content = data.htmlthumb;
				$el.data('bs.popover').tip()
					.add($el)
					.mouseenter(function () {
						$el.data('mouse-in-tooltip', true);
					})
					.mouseleave(function () {
						$el.data('mouse-in-tooltip', true);
						$.hideTooltip($el);
						setTimeout(function () {
							$.hideTooltip($el);
						}, 250);
					});

				$el.popover('show');

				$(".popover-content").css("padding", '0px');
				$(".popover-content").css("max-width", '160px');
				$(".popover-content").css("max-height", '450px');
				$(".popover-content").css("overflow", 'scroll');
				$(".popover-content").css("scrollbar-color", '#506ea2 #eceaea');
				$(".popover-content").css("scrollbar-width", 'thin');
				$(".popover-title").html("<strong>" + $el.data("ndoc_text") + "</strong>");
			}
		});
	};

	$.showTooltip = function ($el, contentUrl) {
		if ($el.data('bs.popover')) {
			if (!$el.data('bs.popover').tip().hasClass('in'))
				$el.popover('show');
		} else {
			$el.popover({
				container: 'body',
				content: 'Cargando thumbails...',
				placement: 'rigth',
				html: true
			}).popover('show');

			$.loadTooltipData($el, contentUrl);
		}
	};

	$.hideTooltip = function ($el) {
		if ($el.data('mouse-in-tooltip') === true && $(".popover:hover").length === 0) {
			$el.popover('hide');
		}
	};

	jQuery('body').on('mouseover', '.gsthumbimg', function (event) {
		var $el = $(this);
		let controller = $(this).data("endpoint");
		$.showTooltip($el, controller);
	});

	jQuery('body').on('change', '.validatstrclose', function (event) {
		jQuery.LoadingStructData();
		var tipoDocumentalId = jQuery(this).val();
		var unidadDocumentalId = jQuery(this.form.unidaddocumental_id).val();

		$.ajax(
			{
				url: '/archivo.php/contenido_documental/verificaCierreExpediente',
				method: 'POST',
				data: jQuery.param({ tipodocumental_id: tipoDocumentalId, unidaddocumental_id: unidadDocumentalId }),
				success: function (response) {
					jQuery.CloseLoadingStructData();

					if (response.status == 200) {
						toastr.info(response.mensaje, "Informacion", { timeOut: 5000, positionClass: 'toast-bottom-full-width' });
					} else {
						toastr.error(response.mensaje, "Error", { timeOut: 5000, positionClass: 'toast-bottom-full-width' });
					}
				},
				complete: function () {
					jQuery.CloseLoadingStructData();
				}

			});

	});

	/**/
	jQuery('body').on('click', '#willdie2', function (event) {
		jQuery('#willdie2').removeClass('docviwermodal'); //la cabeza de la pestania solo servira una vez para invocar el pdf
	});

	jQuery('body').on('click', '#genera_servicio', function (event) {
		jQuery.LoadingStructData();
		jQuery(".servfieldstplcom").hide();

		$.ajax(
			{
				url: '/administracion.php/plantillas_com/loadServicioFields',
				method: 'POST',
				data: jQuery.param({ plantillascom_id: jQuery("#plantillascom_id").val(), genera_servicio: jQuery(this).prop('checked') }),
				success: function (response) {
					jQuery.CloseLoadingStructData();

					if (response.status == 200) {
						jQuery(".servfieldstplcom").html(response.fields_data);
						jQuery(".servfieldstplcom").show();
					} else {
						jQuery(".servfieldstplcom").html('');
					}
				},
				complete: function () {
					jQuery.CloseLoadingStructData();
				}
			});
	});


	function notificationListener() {
		jQuery.LoadingStructData();

		$.ajax(
			{
				url: '/archivo.php/contenido_documental/lastNotification',
				method: 'POST',
				data: jQuery.param({ notificacionprompt_id: 1 }),
				success: function (response) {
					jQuery.CloseLoadingStructData();

					if (response.status == 200) {

						//toastr.info('CODIGO: ' + response.codigo_principal + '  ---  DESCRIPCION: ' + response.descripcion, "NOTIFICACIÓN", {timeOut: 5000, positionClass: 'toast-bottom-full-width'});
						toastr.success('CODIGO: ' + response.codigo_principal + '\nDESCRIPCION: ' + response.descripcion,
							"NOTIFICACIÓN", { timeOut: 5000, positionClass: 'toast-bottom-right' });

					}
				},
				complete: function () {
					jQuery.CloseLoadingStructData();
				}
			});
	}

	var isUpdating = false;
	jQuery('body').on('change', '.requiredXorMaxOneSelect2', function (event) {
		if (isUpdating) return; // prevenir recursión

		const current_object = jQuery(this);
		const current_id = current_object.attr('id');
		const currentValue = current_object.val();

		isUpdating = true;

		jQuery(".requiredXorMaxOneSelect2").each(function (i, row) {
			const element_item = jQuery(row);
			const element_id = element_item.attr('id');

			if (element_id !== current_id) {
				if (element_item.val() && element_item.val() !== '')
					jQuery(element_item).select2('val', '').trigger('change');
			}
		});

		// Validar si ambos están vacíos
		let filledCount = 0;
		jQuery(".requiredXorMaxOneSelect2").each(function (i, row) {
			if (jQuery(row).val() !== null && jQuery(row).val() !== '') {
				filledCount++;
			}
		});

		// Aplicar clase 'required' si ambos vacíos
		jQuery(".requiredXorMaxOneSelect2").each(function (i, row) {
			const element_item = jQuery(row);
			if (filledCount === 0) {
				element_item.addClass('required');
			} else {
				element_item.removeClass('required');
			}
		});

		isUpdating = false;
	});

	jQuery('body').on('click', '.modalviewdoc', function (event) {
		let tabnamedoc = $(this).data("tabnamedoc");
		jQuery('.nav-tabs a[href="#' + tabnamedoc + '"]').tab('show'); //cambia de tab.
		jQuery(".docviwermodal").trigger("click"); //dispara el evento on click de 3 lineas abajo
		jQuery('#willdie2').removeClass('docviwermodal');
	});


	jQuery('body').on('click', '.docviwermodal', function (event) {
		let controller = $(this).data("endpoint");
		$.ajax(
			{
				url: controller,
				type: 'post',
				data: jQuery.param({ comindex_pk: jQuery(this).data("comindex"), q_vars: jQuery(this).data("q_vars"), vtoken: jQuery(this).data("vtoken") }),
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				processData: false,
				beforeSend: function () {
					$.LoadingStructData('Cargando visor de documentos');
				},
				success: function (response) {
					jQuery("#docmodaltrigger").html(response);
				},
				complete: function () {
					$.CloseLoadingStructData();
				},
			});
	});

	jQuery('#saveAdminRuleExp').on("click", function (event) {
		event.preventDefault();
		try {
			$form_container = jQuery(this).closest('form');
			var validator = $form_container.valid();
			if (validator) {
				$.LoadingStructData();
				jQuery.ajax(
					{
						url: '/administracion.php/automatizacion_unidaddoc/update',
						method: 'POST',
						data: $form_container.serialize(),
						contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
						cache: false,
						processData: false,
						success: function (response) {
							if (response.status == 200) {
								parent.jQuery.CloseAndRefreshParent();
								toastr.success(response.message);
							}
							else {
								$.CloseLoadingStructData();
								toastr.error(response.message);
							}
						},
						error: function (response) {
							$.CloseLoadingStructData();
							toastr.error(response.message);
						}
					});
			}
		} catch (error) {
			$.CloseLoadingStructData();
			toastr.error(error);
		}
	});

	jQuery('.autoreulesubserie').on("change", function (event) {
		event.preventDefault();
		try {
			$form_container = jQuery(this).closest('form');
			var $current_select = $(this).val();

			$select_substipodoc = jQuery('#autotipodocumental_id');
			$select_subsmetadato = jQuery('#autosubseriemetadato_id');

			$.LoadingStructData();

			if ($current_select === '') {
				$select_substipodoc.empty().trigger('change');
				$select_subsmetadato.empty().trigger('change');
				$.CloseLoadingStructData();
				return;
			}

			jQuery.ajax(
				{
					url: '/administracion.php/automatizacion_unidaddoc/loadTrdElements',
					method: 'POST',
					data: jQuery.param({ currentselect_id: $current_select }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					success: function (response) {
						if (response.status == 200) {
							$select_substipodoc.empty();
							$select_subsmetadato.empty();

							var items_tipodoc = response.list_items?.itipos_documentales;

							if (Array.isArray(items_tipodoc) && items_tipodoc.length > 0) {
								$select_substipodoc.append('<option value="">Seleccione...</option>');
								$.each(items_tipodoc, function (i, item) {
									var option = new Option(item.text, item.id, false, false);
									$select_substipodoc.append(option);
								});
							} else {
								$select_substipodoc.append('<option value="">Seleccione Subserie...</option>');
							}

							$select_substipodoc.trigger('change.select2');
							$select_substipodoc.val(null).trigger('change');

							var items_metadatos = response.list_items?.imetadatos_subserie;
							if (Array.isArray(items_metadatos) && items_metadatos.length > 0) {
								$.each(items_tipodoc, function (i, item) {
									var option = new Option(item.text, item.id, false, false);
									$select_subsmetadato.append(option);
								});
							}

							$select_subsmetadato.trigger('change.select2');
							$select_subsmetadato.val(null).trigger('change');

							$.CloseLoadingStructData();

							$.highlightSelect2($select_substipodoc);
							$.highlightSelect2($select_subsmetadato);
						}
						else {
							$select_substipodoc.empty().trigger('change');
							$select_subsmetadato.empty().trigger('change');
							$.CloseLoadingStructData();
							toastr.error(response.message);
						}
					},
					error: function (response) {
						$.CloseLoadingStructData();
						toastr.error(response.message);
					}
				});
		} catch (error) {
			$.CloseLoadingStructData();
			toastr.error(error);
		}
	});

	jQuery('.rulemoduloselect').on("change", function (event) {
		event.preventDefault();
		try {
			$form_container = jQuery(this).closest('form');
			var $modulo_select = $(this).val();

			$select_metadato = jQuery('#modulo_metadato');
			$select_etiqueta = jQuery('#auto_exp_etiqueta');
			$select_tipocom = jQuery('#tipocom_id');

			$.LoadingStructData();

			if ($modulo_select === '') {
				$select_metadato.empty().trigger('change');
				$select_etiqueta.empty().trigger('change');
				$select_tipocom.empty();
				$.CloseLoadingStructData();
				return;
			}

			jQuery.ajax(
				{
					url: '/administracion.php/automatizacion_unidaddoc/loadModuloFileds',
					method: 'POST',
					data: jQuery.param({ modulo_select: $modulo_select }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: false,
					success: function (response) {
						if (response.status == 200) {
							var selected_eqfields = $select_etiqueta.val() || [];

							$select_metadato.empty();
							$select_etiqueta.empty();
							$select_tipocom.empty();

							$select_tipocom.append(response.list_typecom);

							if (response.list_items !== null) {
								$.each(response.list_items, function (i, item) {
									var option = new Option(item.text, item.id, false, false);
									$select_metadato.append(option);
								});

								$select_metadato.trigger('change.select2');
								$select_metadato.val(null).trigger('change');
							}

							if (response.list_ietiquetas !== null) {
								$.each(response.list_ietiquetas, function (i, item) {
									var option = new Option(item.text, item.id, false, selected_eqfields.indexOf(item.id.toString()) >= 0);
									$select_etiqueta.append(option);
								});

								$select_etiqueta.trigger('change.select2');
								$select_etiqueta.val(null).trigger('change');
							}

							$.CloseLoadingStructData();

							$.highlightSelect2($select_metadato);
							$.highlightSelect2($select_etiqueta);
							$.highlightSelect2($select_tipocom);
						}
						else {
							$select_metadato.empty().trigger('change');
							$select_etiqueta.empty().trigger('change');
							$select_tipocom.empty();

							$.CloseLoadingStructData();
							toastr.error(response.message);

							$.highlightSelect2($select_metadato);
							$.highlightSelect2($select_etiqueta);
							$.highlightSelect2($select_tipocom);
						}
					},
					error: function (response) {
						$.CloseLoadingStructData();
						toastr.error(response.message);
					}
				});
		} catch (error) {
			$.CloseLoadingStructData();
			toastr.error(error);
		}
	});

	jQuery('body').on('click', '.btntransfer', function (e) {
		e.preventDefault();
		var data_info = jQuery(this).data("comptext");
		var api_endpoint = jQuery(this).data("endpoint");
		var $versionDiv = jQuery(this).closest('.version-item');

		if (data_info != null && data_info.length > 0 && api_endpoint != null && api_endpoint.length > 0) {
			if (confirm("Si continua se remplazara el contenido de la comunicación, con la versión seleccionada") == true) {
				$.ajax({
					url: api_endpoint,
					type: 'post',
					data: jQuery.param({ docscontrolcambio_id: data_info }),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					processData: false,
					beforeSend: function () {
						$.LoadingStructData();
					},
					success: function (response) {
						if (response.status == 200) {
							CKEDITOR.instances.contenido.setData(response.htmlData);
							toastr.success(response.message);

							var spamStr = '<span class="status-badge status-revert tooltip-primary" data-toggle="tooltip" data-original-title="Seleccinada para revertir, pendiente guardar los cambios">Revertido</span>';
							jQuery('span.status-revert').remove();
							$versionDiv.find('.version-number').append(spamStr);
							$versionDiv.find('[data-toggle="tooltip"]').tooltip();
						} else if (response.status == 200) {
							toastr.error(response.message);
						}
					},
					error: function () {
						CKEDITOR.instances.contenido.setData();
						toastr.error('Ocurrio un error consultando la información');
					},
					complete: function () {
						$.CloseLoadingStructData();
					},
				});
			}
		} else {
			toastr.error('Ocurrio un error al consultar los datos');
		}
	});

	jQuery('body').on('mouseover', '.gsthumbimg', function (event) {
		var $el = $(this);
		var controller = $(this).data("endpoint");

		timer = setTimeout(function () {
			if (controller.length > 0)
				$.showTooltip($el, controller);
		}, 1500);
	});

	jQuery('body').on('mouseout', '.gsthumbimg', function (event) {
		window.clearTimeout(timer);
	});

	//ajax al boton icono del list de com interna 
	jQuery('body').on('click', '.docviewerlistmodal', function (event) {
		let controller = $(this).data("endpoint2");

		$.ajax({
			url: controller,
			type: 'post',
			data: jQuery.param({ comindex_pk: jQuery(this).data("comindex"), q_vars: jQuery(this).data("q_vars"), vtoken: jQuery(this).data("vtoken") }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			processData: false,
			beforeSend: function () {
				$.LoadingStructData('Cargando visor de documentos');
			},
			success: function (response) {
				jQuery.OpenModalSIMAD(response.url_viewer);
			},
			complete: function () {
				$.CloseLoadingStructData();
			},
		});
	});

	//ajax al numero de radicado del list de com interna 
	jQuery('body').on('click', '.showviewerlistmodal', function (event) {
		let controller = $(this).data("endpoint");
		$.ajax({
			url: controller,
			type: 'post',
			data: jQuery.param({ comindex_pk: jQuery(this).data("comindex") }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			processData: false,
			beforeSend: function () {
				$.LoadingStructData('Cargando visor de documentos');
			},
			success: function (response) {
				let laUrl = controller;
				jQuery.OpenModalSIMAD(laUrl);
			},
			complete: function () {
				$.CloseLoadingStructData();
			},
		});
	});

	jQuery('body').on('click', '#docs_upload', function (event) {
		var files = $('#filedocs')[0].files;
		// Check file selected or not
		if (files.length > 0) {
			var extension = $('#filedocs').val().split('.').pop().toLowerCase();
			var validFileExtensions = ['zip', '7z', 'rar'];
			if ($.inArray(extension, validFileExtensions) != -1) {
				var form_data = new FormData();
				form_data.append('filedocs', files[0]);

				$.ajax({
					url: '/enviada.php/com_enviada/fileDocsRadMasiva',
					type: 'post',
					data: form_data,
					contentType: false,
					processData: false,
					beforeSend: function () {
						$.LoadingStructData();
					},
					success: function (response) {
						if (response.status == 200) {
							$("#fdocsremoteupdate").html('<input type="hidden" name="filedocsupload" id="filedocsupload" value="' + response.message + '"></input>');
							$("#fdocsremoteupdate").append('<div class="alert alert-success"><strong>Excelente!</strong> ' + response.message + '</div>');
							$("#fdocsremoteupdate").show();
							$("#dataufiledocs").val(response.file_name);
							$("#filedocsupload").val(response.file_name);
							$(".upinfofiles").remove();
						} else {
							$("#fdocsremoteupdate").hide();
							$("#fdocsremoteupdate").empty();
							$("#dataufiledocs").val('');
							$("#filedocsupload").val('');
							alert('Error al subir el archivo ' + response.message);
						}
					},
					complete: function () {
						$.CloseLoadingStructData();
					},
				});
			} else {
				alert("El archivo seleccionado no esta permitido.");
			}
		} else {
			alert("Debe seleccionar un archivo.");
		}
	});

	jQuery('body').on('click', '#docs_upload', function (event) {
		var files = $('#filedocs')[0].files;
		if (files.length > 0) {
			var extension = $('#filedocs').val().split('.').pop().toLowerCase();
			var validFileExtensions = ['zip', '7z', 'rar'];
			if ($.inArray(extension, validFileExtensions) != -1) {
				var form_data = new FormData();
				form_data.append('filedocs', files[0]);

				$.ajax({
					url: '/recibida.php/com_recibida/fileDocsRadMasiva',
					type: 'post',
					data: form_data,
					contentType: false,
					processData: false,
					beforeSend: function () {
						$.LoadingStructData();
					},
					success: function (response) {
						if (response.status == 200) {
							$("#fdocsremoteupdate").html('<input type="hidden" name="filedocsupload" id="filedocsupload" value="' + response.message + '"></input>');
							$("#fdocsremoteupdate").append('<div class="alert alert-success"><strong>Excelente!</strong> ' + response.message + '</div>');
							$("#fdocsremoteupdate").show();
							$("#dataufiledocs").val(response.file_name);
							$("#filedocsupload").val(response.file_name);
							$(".upinfofiles").remove();
						} else {
							$("#fdocsremoteupdate").hide();
							$("#fdocsremoteupdate").empty();
							$("#dataufiledocs").val('');
							$("#filedocsupload").val('');
							alert('Error al subir el archivo ' + response.message);
						}
					},
					complete: function () {
						$.CloseLoadingStructData();
					},
				});
			} else {
				alert("El archivo seleccionado no esta permitido.");
			}
		} else {
			alert("Debe seleccionar un archivo.");
		}
	});

	jQuery('body').on('click', '#btn_enviar', function (event) {
		event.preventDefault();
		var validator = $(this).closest('form').valid();
		if (validator) {
			$.LoadingStructData();
			$.ajax({
				url: '/backend.php/busqueda_avanzada/reportarDinamico',
				method: 'POST',
				data: jQuery(this.form.elements).serialize(),
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				cache: false,
				processData: false,
				success: function (response) {
					$('#recibidor_rta').html(response);
					$.CloseLoadingStructData();
					toastr.success("consulta exitosa!!!");
				},
				error: function (response) {
					$.CloseLoadingStructData();
					toastr.error("Error Interno del Servidor!");
				}
			});
		}
	});

	// radicar masivas
	// Evento botón radicar masivas
	jQuery('body').on('click', '#btn_radicar_masivas', async function (event) {
		event.preventDefault();
		var validator = $(this).closest('form').valid();
		var response_ilist = null;
		var botoneraMigration = $("#hide_buttons_com").html();
		if (validator) {
			try {
				$.UpdateProgressMigMasivo(0, 1, 'Obteniendo registros desde el servidor...');

				const response = await $.ajax({
					url: '/enviada.php/com_enviada/getMigrationsByLote',
					method: 'POST',
					data: $(this.form.elements).serialize(),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: true,
					beforeSend: function () {
						$.LoadingStructData();
						$.CrearProgresoRadMasivo("contenedor_tabla_lote");
						$('#hide_buttons_com').hide();
						$("#hide_buttons_com").empty();
					},
					success: function (response) {
						if (response.status == 200) {
							response_ilist = response.batchs_ilist;
						}
					},
					error: function (response) {
						$.CloseLoadingStructData();
						toastr.error("Error Interno del Servidor!");
						$("#hide_buttons_com").html(botoneraMigration);
						$('#hide_buttons_com').show();
					}
				});

				if (response.status == 200) {
					try {
						if (Array.isArray(response_ilist) && response_ilist.length > 0) {
							$.CloseLoadingStructData();

						}

						$.UpdateProgressMigMasivo(0, response_ilist.length, `Se encontraron ${response_ilist.length} registros. Iniciando procesamiento...`);

						let procesados = 0;
						let exitosos = 0;
						let errores = [];
						// Iterar sobre el array y esperar cada radicación
						for (const item of response_ilist) {
							const identificador = item.pkobject_id || `Registro ${item.pkobject_id}`;
							$.UpdateProgressMigMasivo(procesados, response_ilist.length, `Procesando: <strong>${identificador}</strong>`, `⏳ Procesando: ${identificador}`);
							let resp_process = await $.RadicadorMigMasivaByOne(item.pkobject_id, item.comIdLote, item.comfirma_digital, item.docs_source);

							if (resp_process === null) {
								$.UpdateProgressMigMasivo(procesados + 1, response_ilist.length, `Error en: <strong>${identificador}</strong>`, `✗ Error: ${identificador}`);
								errores.push({
									registro: identificador,
									error: 'Error desconocido'
								});
							} else if (resp_process.status == 200) {
								exitosos++;
								$.UpdateProgressMigMasivo(procesados + 1, response_ilist.length, `Completado: <strong>${identificador}</strong>`, `✓ Completado: ${identificador}`);
							} else {
								$.UpdateProgressMigMasivo(procesados + 1, response_ilist.length, `Error en: <strong>${identificador}</strong>`, `✗ Error: ${resp_process.message}`);
								errores.push({
									registro: identificador,
									error: resp_process.message || 'Error desconocido'
								});
							}

							procesados++;
						}

						$.batchMigResult('{"status":200,"message":"El proceso de radicacion termino"}', exitosos, errores);

					} catch (error) {
						toastr.error("Error procesando los elementos!" + error);
						$("#hide_buttons_com").html(botoneraMigration);
						$('#hide_buttons_com').show();
					}
					finally {
						$.CloseLoadingStructData();
					}
				}
				else {
					$.CloseLoadingStructData();  //cerrar la cortina
					toastr.error("Error Interno del Servidor!," + response.message);
					$('#progresoMigMasivo').remove();
					$("#hide_buttons_com").html(botoneraMigration);
					$('#hide_buttons_com').show();
				}
			} catch (error) {
				toastr.error("Error Interno del Servidor!," + error);
			}
			finally {
				$.CloseLoadingStructData();
			}
		}
	});

	// radicar masivas Actos Administrativos (registro a registro, con barra de progreso)
	// Evento botón radicar masivas Actos Administrativos
	jQuery('body').on('click', '#btn_radicar_masivas_actoadm', async function (event) {
		event.preventDefault();
		var validator = $(this).closest('form').valid();
		var response_ilist = null;
		var botoneraMigration = $("#hide_buttons_com").html();
		if (validator) {
			try {
				$.UpdateProgressMigMasivo(0, 1, 'Obteniendo registros desde el servidor...');

				const response = await $.ajax({
					url: '/comun.php/acto_administrativo/getMigrationsByLote',
					method: 'POST',
					data: $(this.form.elements).serialize(),
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					cache: false,
					processData: true,
					beforeSend: function () {
						$.LoadingStructData();
						$.CrearProgresoRadMasivo("contenedor_tabla_lote");
						$('#hide_buttons_com').hide();
						$("#hide_buttons_com").empty();
					},
					success: function (response) {
						if (response.status == 200) {
							response_ilist = response.batchs_ilist;
						}
					},
					error: function (response) {
						$.CloseLoadingStructData();
						toastr.error("Error Interno del Servidor!");
						$("#hide_buttons_com").html(botoneraMigration);
						$('#hide_buttons_com').show();
					}
				});

				if (response.status == 200) {
					try {
						if (Array.isArray(response_ilist) && response_ilist.length > 0) {
							$.CloseLoadingStructData();
						}

						$.UpdateProgressMigMasivo(0, response_ilist.length, `Se encontraron ${response_ilist.length} registros. Iniciando procesamiento...`);

						let procesados = 0;
						let exitosos = 0;
						let errores = [];
						// Iterar sobre el array y esperar cada radicación
						for (const item of response_ilist) {
							const identificador = item.pkobject_id || `Registro ${item.pkobject_id}`;
							$.UpdateProgressMigMasivo(procesados, response_ilist.length, `Procesando: <strong>${identificador}</strong>`, `⏳ Procesando: ${identificador}`);
							let resp_process = await $.RadicadorMigMasivaByOneActoAdm(item.pkobject_id, item.comIdLote, item.comfirma_digital, item.docs_source);

							if (resp_process === null) {
								$.UpdateProgressMigMasivo(procesados + 1, response_ilist.length, `Error en: <strong>${identificador}</strong>`, `✗ Error: ${identificador}`);
								errores.push({
									registro: identificador,
									error: 'Error desconocido'
								});
							} else if (resp_process.status == 200) {
								exitosos++;
								$.UpdateProgressMigMasivo(procesados + 1, response_ilist.length, `Completado: <strong>${identificador}</strong>`, `✓ Completado: ${identificador}`);
							} else {
								$.UpdateProgressMigMasivo(procesados + 1, response_ilist.length, `Error en: <strong>${identificador}</strong>`, `✗ Error: ${resp_process.message}`);
								errores.push({
									registro: identificador,
									error: resp_process.message || 'Error desconocido'
								});
							}

							procesados++;
						}

						$.batchMigResultAsyncActoAdm('{"status":200,"message":"El proceso de radicacion termino"}', exitosos, errores);

					} catch (error) {
						toastr.error("Error procesando los elementos!" + error);
						$("#hide_buttons_com").html(botoneraMigration);
						$('#hide_buttons_com').show();
					}
					finally {
						$.CloseLoadingStructData();
					}
				}
				else {
					$.CloseLoadingStructData();  //cerrar la cortina
					toastr.error("Error Interno del Servidor!," + response.message);
					$('#progresoMigMasivo').remove();
					$("#hide_buttons_com").html(botoneraMigration);
					$('#hide_buttons_com').show();
				}
			} catch (error) {
				toastr.error("Error Interno del Servidor!," + error);
			}
			finally {
				$.CloseLoadingStructData();
			}
		}
	});

	//function mostrarResultadoFinal(exitosos, errores) {
	$.mostrarResultadoFinal = function (exitosos, errores) {
		const $resultado = $('#resultadoFinal');

		let html = '';
		let claseAlerta = errores.length === 0 ? 'alert-success-migmasiva' : (errores.length < exitosos ? 'alert-warning' : 'alert-danger');
		let icono = errores.length === 0 ? 'glyphicon-ok-circle' : 'glyphicon-exclamation-sign';

		html = `
			<div class="alert ${claseAlerta}" style="margin-bottom: 0;">
				<i class="glyphicon ${icono}"></i>
				<strong style="color: #292929ff;">${errores.length === 0 ? '¡Proceso completado exitosamente!' : 'Proceso finalizado'}</strong><br>
				Exitosos: ${exitosos} | <span style="color: #981b1b;"> Errores: ${errores.length} </span>
			</div>
		`;

		if (errores.length > 0) {
			html += '<div style="margin-top: 10px; max-height: 100px; overflow-y: auto; font-size: 12px;">';
			html += '<strong>Errores encontrados:</strong><ul style="margin-top: 5px;">';
			errores.forEach(err => {
				html += `<li>${err.registro}: ${err.error}</li>`;
			});
			html += '</ul></div>';
		}

		$resultado.html(html).slideDown();


	}

	// Función que radica uno y retorna un <tr> listo
	$.RadicadorMigMasivaByOne = async function (pkobject_id, batchid, comsign, docs_source) {
		try {
			const response = await $.ajax({
				url: '/enviada.php/com_enviada/radicarMigMasivoByOne',
				method: 'POST',
				data: { idmigmasivo: pkobject_id, comIdLote: batchid, comsign_digital: comsign, docs_source: docs_source },
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				cache: false,
				processData: true
			});

			return response;
		} catch (error) {
			return null;
		}
	};

	$.RadicadorMigMasivaByOneActoAdm = async function (pkobject_id, batchid, comsign, docs_source) {
		try {
			const response = await $.ajax({
				url: '/comun.php/acto_administrativo/radicarMigMasivoByOne',
				method: 'POST',
				data: { idmigmasivo: pkobject_id, comIdLote: batchid, comsign_digital: comsign, docs_source: docs_source },
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				cache: false,
				processData: true
			});

			return response;
		} catch (error) {
			return null;
		}
	};

	$.UpdateProgressMigMasivo = function (actual, total, mensaje = '', detalle = null) {
		const porcentaje = Math.round((actual / total) * 100);
		const $barra = $('#barraProgreso');
		const $contador = $('#contadorProgreso');
		const $mensaje = $('#mensajeEstado');
		const $detalles = $('#detallesProceso');

		$barra.css('width', porcentaje + '%')
			.attr('aria-valuenow', porcentaje)
			.text(porcentaje + '%');

		$contador.text(`${actual} / ${total}`);

		if (mensaje) {
			$mensaje.html(mensaje);
		}

		if (detalle) {
			$detalles.show();
			$detalles.prepend(`<div>${detalle}</div>`);
			$detalles.scrollTop(0);
		}

		if (porcentaje === 100) {
			$barra.removeClass('active progress-bar-striped')
				.addClass('progress-bar-success');
		}
	}

	$.CrearProgresoRadMasivo = function (container_element) {
		if ($('#progresoMigMasivo').length) {
			$('#progresoMigMasivo').remove();
		}

		const modalHtml = `
			<div id="progresoMigMasivo" style="width:50%; max-width:90%;text-align: center;margin-left: 25%;">
				<div style="padding: 20px;">
					<h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #337ab7; padding-bottom: 10px;">
						<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Procesando Registros
					</h3>
					
					<div class="progress" style="margin-top: 20px; height: 30px; margin-bottom: 10px;">
						<div id="barraProgreso" class="progress-bar progress-bar-striped active" 
							role="progressbar" 
							aria-valuenow="0" 
							aria-valuemin="0" 
							aria-valuemax="100" 
							style="width: 0%; min-width: 40px; line-height: 30px; font-size: 14px; font-weight: bold;">
							0%
						</div>
					</div>
					
					<div style="text-align: center; color: #666; margin-bottom: 15px;">
						<span id="contadorProgreso">0 / 0</span> registros procesados
					</div>
					
					<div class="well well-sm" style="min-height: 60px;margin-bottom: 0px !important;">
						<strong>Estado:</strong>
						<div id="mensajeEstado" style="margin-top: 8px; color: #555;">
							Iniciando proceso...
						</div>
					</div>
					
					<div id="detallesProceso" class="detalles-proceso" style="max-height: 150px !important; overflow: auto;border: solid 1px #acd9f7;padding-top: 10px;"></div>
				</div>
			</div>
		`;

		$('#' + container_element).append(modalHtml);
	}

	jQuery('body').on('click', '#btnemailanular', function (event) {
		event.preventDefault();
		if ($(this).closest('form').valid()) {
			$.LoadingStructData();
			$.ajax({
				url: '/recibida.php/email_sync/updateAnular',
				method: 'POST',
				data: jQuery(this.form.elements).serialize(),
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				cache: false,
				processData: false,
				success: function (response) {
					if (response.error == false) {
						parent.jQuery('body').find('tr[data-id="' + response.element_update + '"]').html(response.resp_html);
						parent.jQuery.CloseModalSIMAD();
						parent.toastr.success(response.message);
					} else {
						toastr.error(response.message);
					}
					$.CloseLoadingStructData();
				},
				error: function (response) {
					$.CloseLoadingStructData();
					toastr.error("Error Interno del Servidor!");
				}
			});
		}
	});

	jQuery('body').on('click', '#btnasignaremail', function (event) {
		event.preventDefault();
		$.LoadingStructData();

		$.ajax({
			url: '/recibida.php/email_sync/asignarEmail',
			method: 'POST',
			data: jQuery.param({ email_id: $(this).data('idelement') }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			success: function (response) {
				if (response.error == false) {
					parent.jQuery('body').find('tr[data-id="' + response.element_update + '"]').html(response.resp_html);
					parent.jQuery.CloseModalSIMAD();
					parent.toastr.success(response.message);
					$('[data-toggle="tooltip"]').tooltip();
				} else {
					toastr.error(response.message);
				}
				$.CloseLoadingStructData();
			},
			error: function (response) {
				$.CloseLoadingStructData();
				toastr.error("Error Interno del Servidor!");
			}
		});
	});

	jQuery('body').on('click', '.confirm-link', async function (event) {
		event.preventDefault();
		try {
			await $.CustomConfirmEvent(this);
		}
		catch (error) {
			toastr.error("Error Interno del Servidor!");
		}
	});

	jQuery('body').on('click', '#save_and_send', async function (event) {
		event.preventDefault();
		if ($(this).closest('form').valid()) {
			try {
				await $.CustomConfirmEvent(this);
			} catch (error) {
				toastr.error("Error Interno del Servidor!");
			}
		}
	});

	jQuery('body').on('click', '#filesharedupload', function (event) {
		event.preventDefault();
		if ($(this).closest('form').valid()) {
			$.ajax({
				url: '/recibida.php/email_sync/sharedUpload',
				method: 'POST',
				data: $(this.closest('form')).serialize(),
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				cache: false,
				processData: false,
				beforeSend: function () {
					$.LoadingStructData();
				},
				success: function (response) {
					if (response.error == false) {
						parent.jQuery('.' + response.element_update).html(response.resp_html);
						parent.toastr.success(response.message);
						parent.jQuery.CloseModalSIMAD();
						$('[data-toggle="tooltip"]').tooltip();
					} else {
						parent.toastr.error('Ocurrio un error => ' + response.message);
					}

					$.CloseLoadingStructData();
				},
				error: function (response) {
					$.CloseLoadingStructData();
					toastr.error("Error Interno del Servidor!");
				}
			});
		}
	});

	jQuery('body').on('click', '#btnreleasedemail', function (event) {
		event.preventDefault();
		$.LoadingStructData();

		$.ajax({
			url: '/recibida.php/email_sync/releaseEmail',
			method: 'POST',
			data: jQuery.param({ email_id: $(this).data('idelement') }),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			success: function (response) {
				if (response.error == false) {
					parent.jQuery('body').find('tr[data-id="' + response.element_update + '"]').html(response.resp_html);
					parent.jQuery.CloseModalSIMAD();
					parent.toastr.success(response.message);
					$('[data-toggle="tooltip"]').tooltip();
				} else {
					toastr.error(response.message);
				}
				$.CloseLoadingStructData();
			},
			error: function (response) {
				$.CloseLoadingStructData();
				toastr.error("Error Interno del Servidor!");
			}
		});
	});

	jQuery('body').on('click', '#btn_exportar2', function (event) {
		var url = '/backend.php/busqueda_avanzada/exportarExcel';
		jQuery.OpenModalSIMAD(url);
	});

	jQuery('body').on('click', '#expReporte', function (event) {
		event.preventDefault();
		jQuery.ExportReportSave(this);
	});

	jQuery('body').on('click', '#expFinReporte', function (event) {
		event.preventDefault();

		const $form = jQuery(this).closest('form');
		const data = $form.serializeArray(); //serializar.
		const valores = jQuery('#duallistbox_report').val() || [];
		data.push({ name: 'campos_export', value: valores.join(';') });

		$.ajax({
			type: 'POST',
			url: '/backend.php/busqueda_avanzada/guardarReporte',
			data: jQuery.param(data),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			beforeSend: function () {
				$.LoadingStructData();
			},
			success: function (response) {
				if (response.status == 200) {
					$.CloseLoadingStructData();
					toastr.success(response.mensaje);
				}
				else if (response.status == 403) {
					$.CloseLoadingStructData();
					toastr.error(response.mensaje);
				}
				else {
					$.CloseLoadingStructData();
					toastr.error(response.mensaje);
				}
			},
			error: function (response) {
				$.CloseLoadingStructData();
				toastr.error('Error Interno del Servidor!');
			}
		});

	});

	// desde exportarExcelSuccess.php
	jQuery('body').on('click', '#expExcel', function (event) {
		event.preventDefault();
		$.LoadingStructData();
		$.ajax({
			type: 'POST',
			url: '/backend.php/busqueda_avanzada/generarTablaExcel',
			data: jQuery(this.form.elements).serialize(),
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache: false,
			processData: false,
			beforeSend: function () {
				$.LoadingStructData();
			},
			success: function (data) {
				$.CloseLoadingStructData();
				try {
					if (data.status == 200) {
						toastr.success(data.message);
						if (data.url_download.length > 0) { window.open(data.url_download, '_blank'); }
					}
					else if (data.httpStatus == 400) {
						toastr.error(data.message);
					}
					else {
						toastr.warning(data.message);
					}
				} catch (err) {
					toastr.error(err.message);
				}
			},
			error: function (response) {
				$.CloseLoadingStructData();
			},
			complete: function () {
				$.CloseLoadingStructData();
			},
		});
	});

	//para escoger el reporte y cargarlo a la parte derecha.
	jQuery('body').on('change', '.reportuserlist', async function (event) {
		var usuarioReporteId = jQuery(this).val();
		const $selectldual = $('#duallistbox_report');
		const errores = [];

		if (usuarioReporteId.length === 0) {
			jQuery('#rowfieldsave').html('');
			jQuery('#rowfieldsave').hide();
			return;
		}

		try {
			$.LoadingStructData();
			const response = await $.ajax(
				{
					url: '/backend.php/busqueda_avanzada/loadStoredReport',
					method: 'POST',
					data: jQuery.param({ usuario_reporte_id: usuarioReporteId })
				});

			if (response.status !== 200) {
				toastr.error(resp.mensaje, "Error", { timeOut: 5000, positionClass: 'toast-bottom-full-width' });
				return;
			}

			$selectldual.prop('selected', false);
			var valuesToSelect = response.campos
			// Seleccionar los valores deseados
			$.each(valuesToSelect, function (index, texto) {
				const $option = $selectldual.find('option[value="' + texto + '"]');
				if ($option.length > 0) {
					$option.prop('selected', true);
				} else {
					erros_any.push(value);
				}
			});

			if (errores.length) {
				toastr.error("No se encontraron estos valores: " + errores.join(', '), "Error", { timeOut: 5000, positionClass: 'toast-bottom-full-width' });
				return;
			}

			if ($('#expReporte').length) {
				await $.ExportReportSave($('#expReporte'), response);
			}

			$selectldual.bootstrapDualListbox('refresh');
			toastr.info(response.mensaje, "Información", { timeOut: 5000, positionClass: 'toast-bottom-full-width' });

		} catch (error) {
			toastr.error("Error al cargar la selección", "Error", { timeOut: 5000, positionClass: 'toast-bottom-full-width' });
		} finally {
			$.CloseLoadingStructData();
		}
	});

	/*jQuery('body').on('change','#dependencia_id', function (event)
	{
		var valorSeleccionado = parseInt(jQuery(this).val());
		var valorActivador = jQuery('#div_regional_destino').data('valoractivador');

		if (valorSeleccionado === valorActivador.toString()) 
		{
			jQuery.ajax( 
			{
			type:'POST',
			url: '/recibida.php/com_recibida/regionalesDest',
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			cache:false,
			processData: false,
			success: function(response)
			{
				if(response.status == 200)
				{
					var nuevoDiv = response.text_select;
					jQuery('#div_regional_destino').html(nuevoDiv);
					jQuery('#div_regional_destino').show();
					$.highlightElement(jQuery('#regional_destino_id'));
				}
				else if(response.status == 400)
				{
					jQuery('#div_regional_destino').hide();
				}
				else
				{
					jQuery('#div_regional_destino').hide();
				}
			},
			error: function(response)
			{
				jQuery('#div_regional_destino').hide();
			},
			complete: function()
			{
			},
			});
		} 
		else 
		{
			jQuery('#div_regional_destino').html('');
			jQuery('#div_regional_destino').hide();
		}
	});*/

	// Funcion para abrir Modal Fancybox
	// Parametros URL, Ancho, Alto
	$.OpenModalCloseCallback = function (url, ancho, alto) {
		$lastOpener = $(document.activeElement);
		if (typeof (ancho) === undefined) ancho = 800;
		if (typeof (alto) === undefined) alto = 600;

		$.fancybox.open({
			href: url,
			type: 'iframe',
			maxWidth: ancho,
			maxHeight: alto,
			fitToView: false,
			width: '99%',
			height: '99%',
			autoSize: false,
			closeClick: false,
			openEffect: 'none',
			closeEffect: 'none',
			afterClose: function () {
			},
			beforeShow: function () {
				setTimeout(function () {
					var $iframe = $('.fancybox-iframe');
					try {
						// Inyectar CSS en el iframe
						var $head = $iframe.contents().find('head');
						$head.append('<style>' +
							'input:focus, textarea:focus, select:focus {' +
							'border: 1px solid #007bff !important;' +
							'color: #333;' +
							'outline: none !important;' +
							'box-shadow: 0 0 5px rgba(0, 123, 255, 0.5) !important;' +
							'}' +
							'</style>');
					} catch (e) {
						//console.log('No se puede modificar el CSS del iframe (cross-domain)');
					}
				}, 500);
			},
			afterShow: function () {
				const firstInput = $(".fancybox-iframe").contents().find('input.form-control.input-sm:not([type="hidden"]):not([type="submit"]):not([type="button"]), select:not([id="serie_id"]):not([id="subserie_id"])').filter(':visible').first();
				setTimeout(function () {
					firstInput.focus();
				}, 200);
			},
			afterClose: function () {
				$.restoreFocus();
			}
		});
	};

	$.NotesDocProcessAp = function (response, ielement) {
		try {
			var response_value = JSON.parse(response);
			if (response_value.status == 200) {
				var selement = $('li.' + ielement);
				$('li.' + ielement + ' .action-links .noteactions').remove();
				$('li.' + ielement + ' p.comment-text').text(response_value.message);
			}
			$.CloseLoadingStructData();
		} catch (err) {
			$.CloseLoadingStructData();
		}
	};

	// Funcion para Cerrar Modal FancyBox
	$.CloseModalSIMAD = function () {
		$.fancybox.close();
	};

	// Funcion para Cerrar Modal FancyBox and reload parent
	$.ReloadAndCloseModalSIMAD = function () {
		$.fancybox.close();
		window.location.reload();
	};

	// Funcion verificar si fancybox esta isopen
	$.FancyBoxIsOpen = function (url_main) {
		var divfc = 0;
		try {
			divfc = parent.jQuery('.fancybox-opened').length;
		}
		catch (error) {
		}
		//*******************************************
		if (divfc) {
			$.CloseModalSIMAD()
			$.CloseModalAndHrefParent(url_main);
		} else {
			window.parent.location.href = url_main;
		}
	};

	// Funcion para Seleccionar Subserie - Modulo Archivo
	$.SeleccionarSubserieArchivo = function (permiso) {
		$.OpenModalSIMAD('/archivo.php/unidad_documental/subserie?permiso=' + permiso, 450, 240);
	};

	// Funcion para Seleccionar Subserie Modal - Archivo
	$.SeleccionarRetornarModal = function () {
		id_subserie = $('#subseries').val();

		if (id_subserie != 0) {
			parent.jQuery('#subserie_id').val(id_subserie);
			parent.jQuery('#serie_documental').val($("#subseries option:selected").text());
			parent.jQuery.CloseModalSIMAD();
		} else {
			alert('Debe seleccionar una Subserie');
		}
	};

	$.isElementVisible = function ($element) {
		// Verificaciones básicas
		if (!$element.is(':visible') ||
			$element.is(':disabled') ||
			$element.css('display') === 'none' ||
			$element.css('visibility') === 'hidden' ||
			$element.css('opacity') === '0') {
			return false;
		}

		// Verificar dimensiones
		if ($element.width() <= 0 || $element.height() <= 0) {
			return false;
		}

		// Verificar si está fuera de la pantalla (opcional)
		var rect = $element[0].getBoundingClientRect();
		return rect.top >= 0 && rect.left >= 0 &&
			rect.bottom <= $(window).height() &&
			rect.right <= $(window).width();
	}

	// Funcion para Retornar valor a Campo
	$.RetornarCampoFomulario = function (id_campo, valor) {
		$('#' + id_campo).val(valor);
		$.CloseModalSIMAD();
	};

	// Funcion para Retornar valor a Campo
	$.LimpiarCampoFormulario = function (id_campo) {
		$('#' + id_campo).val('');
	};

	// Oculta Barra de Navegacion en Iframe
	/*if (window.top!=window.self){
		$(".page-container").removeClass('horizontal-menu');
		$(".page-container .navbar").remove();
	}*/

	// Funcion para Cargar campos del codigo marc
	$.CargaContenidoMarc = function (selectACargar, basepath) {
		codigo_marc = 'codigo'
		subcampo = 'subcampo'
		indicadores = 'indicadores'
		campos = 'campo'

		if (selectACargar == 'codigo') {
			selectAnterior = campos;
		}

		if (selectACargar == 'subcampo') {
			selectAnterior = codigo_marc;
		}

		if (selectACargar == 'indicadores') {
			selectAnterior = subcampo;
		}

		var valor = document.getElementById(selectAnterior).options[document.getElementById(selectAnterior).selectedIndex].value;
		var elemento;

		if (valor != '') {
			ajax = $.NuevoAjax();
			ajax.open('GET', basepath + '/documentacion.php/documentacion_tecnica/campos?id_seleccion=' + valor + '&combo_box=' + selectACargar, true);

			ajax.onreadystatechange = function () {
				if (ajax.readyState == 1) {

					elemento = document.getElementById(selectACargar);
					elemento.length = 0;

					var opcionCargando = document.createElement('option');
					opcionCargando.value = 0;
					opcionCargando.innerHTML = 'Cargando...';

					elemento.appendChild(opcionCargando);
					elemento.disable = true;

				}

				if (ajax.readyState == 4) {

					document.getElementById('contenedor_' + selectACargar).innerHTML = ajax.responseText;

				}
			}
			ajax.send(null);
		}

		var x = 1;
		var y = null;

		while (x <= 2) {
			if (x == 1) {
				comboActual = 'campo';
				//comboActual = 'codigo';		
			}
			if (x == 2) {
				comboActual = 'codigo';
				//comboActual = 'subcampo';
			}

			valor = document.getElementById(comboActual).options[document.getElementById(comboActual).selectedIndex].value;
			if (valor == '') {
				while (x <= 4) {
					y = x + 1;
					if (y == 2) {
						comboActual_2 = 'codigo';
					}
					if (y == 3) {
						comboActual_2 = 'subcampo';
					}
					if (y == 4) {
						comboActual_2 = 'indicador1';
					}
					if (y == 5) {
						comboActual_2 = 'indicador2';
					}
					elemento = document.getElementById(comboActual_2);
					elemento.length = 0;
					var opcionSelecciona = document.createElement('option');
					opcionSelecciona.value = 0;
					opcionSelecciona.innerHTML = 'Seleccione Opcion...';
					elemento.appendChild(opcionSelecciona);
					elemento.disabled = true;
					x++;
				}
			}
			x++;
		}
	};

	//funcion cargar combos workflow
	$.CargaContenidoWorkflow = function (selectACargar, basepath, editwf) {
		usuarios = 'usuario_id'
		actividades = 'actividades'
		IsWfEdit = typeof editwf !== 'undefined' ? '&editwf=' + editwf : '';

		if (selectACargar == 'usuario_id') {
			selectAnterior = actividades;
		}

		var valor = document.getElementById(selectAnterior).options[document.getElementById(selectAnterior).selectedIndex].value;
		var elemento;

		if (valor != '') {
			ajax = $.NuevoAjax();
			ajax.open('GET', basepath + '/administracion.php/wf_instancia_bitacora/usuarios?id_seleccion=' + valor + IsWfEdit + '&combo_box=' + selectACargar, true);
			ajax.onreadystatechange = function () {
				if (ajax.readyState == 1) {
					elemento = document.getElementById(selectACargar);
					//elementtr = document.getElementById('tr_usuarios');
					elemento.length = 0;

					var opcionCargando = document.createElement('option');
					opcionCargando.value = 0;
					opcionCargando.innerHTML = 'Cargando...';

					elemento.appendChild(opcionCargando);
					elemento.disable = true;
					//elementtr.style.display = "none";
					elementodiv = document.getElementById('contenedor_' + selectACargar);
					elementodiv.style.display = "inline";
				}

				if (ajax.readyState == 4) {
					document.getElementById('contenedor_' + selectACargar).innerHTML = ajax.responseText;
				}
			}
			ajax.send(null);
		}

		var x = 1;
		var y = null;

		while (x <= 2) {
			if (x == 1) {
				comboActual = 'actividades';
			}
			if (x == 2) {
				comboActual = 'actividades';
			}

			valor = document.getElementById(comboActual).options[document.getElementById(comboActual).selectedIndex].value;
			if (valor == '') {
				while (x <= 2) {
					y = x + 1;
					if (y == 2) {
						comboActual_2 = 'usuario_id';
					}

					elemento = document.getElementById(comboActual_2);
					elemento.length = 0;
					var opcionSelecciona = document.createElement('option');
					opcionSelecciona.value = 0;
					opcionSelecciona.innerHTML = 'Seleccione Opcion...';
					elemento.appendChild(opcionSelecciona);
					elemento.disabled = true;
					elementodiv = document.getElementById('contenedor_' + selectACargar);
					elementodiv.style.display = "none";
					x++;
				}
			}
			x++;
		}
	}

	/*$.formatState = function (opt) {
		if (!opt.id) {
			return opt.text;
		}               
		var optimage = $(opt.element).data('image'); 
		if(!optimage){
			return opt.text;
		} else {                    
			var $opt = $(
				'<span><img src="' + optimage + '" width="23px" /> ' + opt.text + '</span>'
			);
			return $opt;
		}
	};*/

	$.ArchFileExtMetadata = function (fname) {
		$.ajax(
			{
				url: '/archivo.php/contenido_documental/readFileMetadata',
				method: 'POST',
				data: jQuery.param({ fname: fname }),
				beforeSend: function () {
					$.LoadingStructData('Leyendo metadatos del archivo...');
				},
				error: function (response) {
					toastr.error("Ocurrio un error, no se realizo la actividad solicitada");
				},
				success: function (response) {
					if (response.status == 200) {
						jQuery('.formatcustom').val(response.total_pages).removeClass("data-readonly");
						jQuery('.formattypecust').show().removeClass("data-readonly");
						jQuery('.choicecustom').val("").removeClass("data-readonly");
					} else {
						toastr.error("Ocurrio un error, no se realizo la actividad solicitada");
					}
				},
				complete: function () {
					$.CloseLoadingStructData();
				}
			});
	};

	$.cargarContenidoSelect = function (baseurl, fname, update_div) {
		// Send data to the server
		$.ajax(
			{
				url: baseurl,
				method: 'POST',
				data: $('#' + fname).serialize(),
				beforeSend: function () {
					$.LoadingStructData('Cargando tipos documentales');
				},
				error: function (response) {
					alert("Error, no se pudo cargar la informacion solicitada!");
				},
				success: function (response) {
					// set content response to div
					$("div#" + update_div).html(response);
				},
				complete: function () {
					$.CloseLoadingStructData();
				}
			});
	};

	$.NuevoAjax = function (selectACargar) {
		var xmlhttp = false;
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (E) { xmlhttp = false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest != 'undefined') { xmlhttp = new XMLHttpRequest(); }
		return xmlhttp;
	};

	$.CloseModalAndHrefParent = function (url_reload) {
		if ($.trim(url_reload)) {
			window.parent.location.href = url_reload;
		}
	};

	$.CloseModalRefreshParent = function () {
		parent.jQuery.CloseModalSIMAD();
	};

	$.CloseAndRefreshParent = function () {
		parent.jQuery.CloseModalSIMAD();
		window.parent.location.reload();
	};

	$.RefreshCurrentForm = function () {
		window.location.reload(true);
	};

	$.LoadingStructData = function (str_wait) {
		let str_default = 'Cargando informacion...';
		if (typeof str_wait === "string" && str_wait.length === 0) {
			str_wait = str_default;
		}
		waitingDialog.show(str_wait);
	};

	$.CloseLoadingStructData = function () {
		waitingDialog.hide();
	};

	$.CreateComValid = function (response, elupdateid) {
		waitingDialog.hide();
		var opts = {
			"closeButton": true,
			"debug": false,
			"positionClass": "toast-bottom-right",
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
		toastr.success('La actividad se completo satisfactoriamente.', 'Actividad Masiva', opts);
		$('.form-wizard').find("a[href*='tab2-3']").trigger('click');
		$('#' + elupdateid).html(response);
	};

	$("form").submit(function (event) {
		var validator = $(this).closest('form').validate();
		if (validator.form()) {
			waitingDialog.show('Cargando informacion...');
		} else {
			event.preventDefault();
		}
	});


	$(".usersvalidate").change(function () {
		if ($(this).attr('id') == "usuario_causador") {
			if ($("#usuario_causador").val() == "") {
				$("#usuario_proveedores").val("");
				$('#usuario_proveedores').attr('disabled', false);
			} else {
				$("#usuario_proveedores").val("");
				$('#usuario_proveedores').attr('disabled', true);
				$("#usuario_proveedores").parent().removeClass('validate-has-error');
				$("#usuario_proveedores").parent().find("span.validate-has-error").remove();
			}
		} else if ($(this).attr('id') == "usuario_proveedores") {
			if ($("#usuario_proveedores").val() == "") {
				$("#usuario_causador").val("");
				$('#usuario_causador').attr('disabled', false);
			} else {
				$("#usuario_causador").val("");
				$('#usuario_causador').attr('disabled', true);
				$("#usuario_causador").parent().removeClass('validate-has-error');
				$("#usuario_causador").parent().find("span.validate-has-error").remove();
			}
		}
	});

	$(".factvalor-group").blur(function () {
		if ($(this).attr('id') == "valor_factura") {
			if ($(this).val() == "") {
				$(this).val("");
				//$("#valor_factura_us").val("");
				$('#valor_factura_us').attr('disabled', false);
				//$("#valor_factura_e").val("");
				$('#valor_factura_e').attr('disabled', false);
			} else {
				$("#valor_factura_us").parent().removeClass('validate-has-error');
				$("#valor_factura_us").parent().find("span.validate-has-error").remove();
				$("#valor_factura_e").parent().removeClass('validate-has-error');
				$("#valor_factura_e").parent().find("span.validate-has-error").remove();
				$("#valor_factura_us").val("");
				$('#valor_factura_us').attr('disabled', true);
				$("#valor_factura_e").val("");
				$('#valor_factura_e').attr('disabled', true);
			}
		} else if ($(this).attr('id') == "valor_factura_us") {
			if ($(this).val() == "") {
				$(this).val("");
				//$("#valor_factura").val("");
				$('#valor_factura').attr('disabled', false);
				//$("#valor_factura_e").val("");
				$('#valor_factura_e').attr('disabled', false);
			} else {
				$("#valor_factura_e").parent().removeClass('validate-has-error');
				$("#valor_factura_e").parent().find("span.validate-has-error").remove();
				$("#valor_factura").parent().removeClass('validate-has-error');
				$("#valor_factura").parent().find("span.validate-has-error").remove();
				$("#valor_factura").val("");
				$('#valor_factura').attr('disabled', true);
				$("#valor_factura_e").val("");
				$('#valor_factura_e').attr('disabled', true);
			}
		} else if ($(this).attr('id') == "valor_factura_e") {
			if ($(this).val() == "") {
				$(this).val("");
				//$("#valor_factura").val("");
				$('#valor_factura').attr('disabled', false);
				//$("#valor_factura_us").val("");
				$('#valor_factura_us').attr('disabled', false);
			} else {
				$("#valor_factura_us").parent().removeClass('validate-has-error');
				$("#valor_factura_us").parent().find("span.validate-has-error").remove();
				$("#valor_factura").parent().removeClass('validate-has-error');
				$("#valor_factura").parent().find("span.validate-has-error").remove();
				$("#valor_factura").val("");
				$('#valor_factura').attr('disabled', true);
				$("#valor_factura_us").val("");
				$('#valor_factura_us').attr('disabled', true);
			}
		}
	});

	$('input.currencyMask').currencyInput();

	$(".currencyMask").change(function () {
		$(this).currencyInput();
		/*if (!$.isNumeric($(this).val()))
			$(this).val('0').trigger('change');    
		$(this).val(parseFloat($(this).val(), 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());*/
	});

	// Función para mostrar modal de confirmación
	$.mostrarConfirmacion = function (segundos = 5, message) { // 5 segundos para demo rápida
		return new Promise((resolve) => {
			let timeLeft = segundos;
			let resuelto = false;

			const $modal = $(`
				<div id="confirmationModal" class="modal-overlay">
					<div class="modal-content">
						<div class="modal-header">
							<h2 class="modal-title">⚠️ Comunicaci&oacute;n Enviada</h2>
							<p class="modal-message" id="modalMessage">
								${message}.
							</p>
						</div>
						
						<div class="time-remaining" id="timeRemaining">
							Tiempo restante: <span id="countdown">${timeLeft}s</span> segundos
						</div>
						
						<div class="progress-container">
							<div class="progress-bar" id="progressBar"></div>
						</div>
						
						<div class="modal-buttons">
							<button type="button" class="btn-cancel" id="cancelBtn">
								Cancelar
							</button>
						</div>
					</div>
				</div>
			`);

			$('body').append($modal);
			jQuery('#progressBar').css('width', '0%');
			jQuery('#confirmationModal').fadeIn(300);

			const resolverYLimpiar = (resultado) => {
				if (!resuelto) {
					resuelto = true;
					clearInterval(interval);
					jQuery('#confirmationModal').remove();
					resolve(resultado);
				}
			};

			const interval = setInterval(() => {
				timeLeft--;
				$('#countdown').text(timeLeft);
				const progreso = (timeLeft / segundos) * 100;
				$('#progressBar').css('width', progreso + '%');

				if (timeLeft <= 0) {
					resolverYLimpiar(true);
				}
			}, 1000);

			$('#cancelBtn').click(() => {
				resolverYLimpiar(false);
			});
		});
	}

	/**
	 * Module for displaying "Waiting for..." dialog using Bootstrap
	 *
	 * @author Eugene Maslovich <ehpc@em42.ru>
	 */

	var waitingDialog = waitingDialog || (function ($) {
		'use strict';

		// Creating modal dialog's DOM
		var $dialog = $(
			'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
			'<div class="modal-dialog modal-m">' +
			'<div class="modal-content">' +
			'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
			'<div class="modal-body">' +
			'<div class="progress progress-striped active"><div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>' +
			'</div>' +
			'</div></div></div>');

		return {
			/**
			 * Opens our dialog
			 * @param message Custom message
			 * @param options Custom options:
			 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
			 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
			 */
			show: function (message, options) {
				// Assigning defaults
				if (typeof options === 'undefined') {
					options = {};
				}
				if (typeof message === 'undefined') {
					message = 'Cargando informaci\u00f3n...';
				}
				var settings = $.extend({
					dialogSize: 'm',
					progressType: 'success',
					onHide: null // This callback runs after the dialog was hidden
				}, options);

				// Configuring dialog
				$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
				$dialog.find('.progress-bar').attr('class', 'progress-bar');
				if (settings.progressType) {
					$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
				}
				$dialog.find('h3').text(message);
				// Adding callbacks
				if (typeof settings.onHide === 'function') {
					$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
						settings.onHide.call($dialog);
					});
				}
				// Opening dialog
				$dialog.modal();
			},
			/**
			 * Closes dialog
			 */
			hide: function () {
				$dialog.modal('hide');
			}
		};
	})(jQuery);

	// Requerimiento 4: obligatoriedad de "Requiere respuesta" al archivar comunicaciones (com_interna/transferencia)
	function actualizarReqRespuesta($switch) {
		var $group = $switch.closest('.form-group');
		var $wrapper = $group.find('.reqresp-obs-wrapper');
		var $obs = $wrapper.find('.reqresp-obs');
		var $hidden = $group.find('.reqresp-hidden');
		var marcado = $switch.is(':checked');

		$hidden.val(marcado ? '1' : '0');

		if (marcado) {
			$wrapper.hide();
			$obs.removeClass('required').removeClass('error');
			$obs.next('label.error').remove();
		} else {
			$wrapper.show();
			$obs.addClass('required');
		}
	}

	jQuery('body').on('change click', '.reqresp-switch', function (event) {
		actualizarReqRespuesta(jQuery(this));
	});

	jQuery('.reqresp-switch').each(function () {
		actualizarReqRespuesta(jQuery(this));
	});

	if (jQuery('#actoadminEtapaSortable').length && jQuery.fn.sortable) {
		jQuery('#actoadminEtapaSortable').sortable({
			items: 'tr',
			handle: '.actoadmin-etapa-drag',
			axis: 'y',
			helper: function (e, tr) {
				var $originals = tr.children();
				var $helper = tr.clone();
				$helper.children().each(function (index) {
					jQuery(this).width($originals.eq(index).outerWidth());
				});
				return $helper;
			},
			update: function () {
				var ordenIds = [];
				jQuery('#actoadminEtapaSortable tr').each(function (index) {
					ordenIds.push(jQuery(this).data('etapa-id'));
					jQuery(this).find('.actoadmin-etapa-orden').text(index + 1);
				});
				jQuery.ajax({
					url: '/administracion.php/actoadmin_etapa/reorder',
					type: 'POST',
					data: { orden_ids: ordenIds },
					success: function () { },
					error: function () {
						alert('Ocurrió un error guardando el nuevo orden, por favor intente de nuevo.');
						window.location.reload();
					}
				});
			}
		});
	}

	// UARIV-202605 (ampliación): modal/pestaña "Configurar Flujo" - arrastrar para reordenar
	// participantes y resaltar empates de orden (dos o más con el mismo número).
	function actualizarOrdenFlujo($lista) {
		var conteos = {};
		var ordenMaximo = null;
		$lista.find('.flujo-order-input').each(function () {
			var val = jQuery(this).val();
			if (val !== '') {
				conteos[val] = (conteos[val] || 0) + 1;
				var num = parseInt(val, 10);
				if (ordenMaximo === null || num > ordenMaximo) { ordenMaximo = num; }
			}
		});
		var hayEmpate = false;
		var rolesEnOrdenMaximo = [];
		$lista.find('.flujo-participant-item').each(function () {
			var $item = jQuery(this);
			var val = $item.find('.flujo-order-input').val();
			var $badge = $item.find('.flujo-order-badge');
			$badge.text(val !== '' ? val : '-');
			if (val !== '' && conteos[val] > 1) {
				$badge.addClass('flujo-order-badge-tie');
				hayEmpate = true;
			} else {
				$badge.removeClass('flujo-order-badge-tie');
			}
			if (ordenMaximo !== null && parseInt(val, 10) === ordenMaximo) {
				rolesEnOrdenMaximo.push($item.data('rol-id'));
			}
		});
		$lista.closest('.tab-pane').find('.flujo-tie-alert').toggle(hayEmpate);
		//*************************************************************************************************
		// UARIV-202605: el/los participante(s) con el orden más alto deben ser Firmante(s) (rol 2).
		var faltaFirmanteAlFinal = false;
		if (ordenMaximo !== null) {
			faltaFirmanteAlFinal = rolesEnOrdenMaximo.length === 0 || rolesEnOrdenMaximo.some(function (rol) { return rol != 2; });
		}
		var $btnGuardar = $lista.closest('.tab-content').siblings('.flujo-actions').find('input[name="guardarFlujo"]');
		$lista.closest('.tab-pane').find('.flujo-firmante-alert').toggle(faltaFirmanteAlFinal);
		$btnGuardar.prop('disabled', faltaFirmanteAlFinal);
	}

	jQuery('.flujo-participant-list').each(function () {
		actualizarOrdenFlujo(jQuery(this));
	});

	jQuery('body').on('input change', '.flujo-order-input', function () {
		actualizarOrdenFlujo(jQuery(this).closest('.flujo-participant-list'));
	});

	if (jQuery.fn.sortable) {
		jQuery('.flujo-participant-list').sortable({
			handle: '.flujo-drag-handle',
			axis: 'y',
			placeholder: 'flujo-participant-placeholder',
			update: function () {
				var $lista = jQuery(this);
				$lista.find('.flujo-participant-item').each(function (index) {
					jQuery(this).find('.flujo-order-input').val(index + 1);
				});
				actualizarOrdenFlujo($lista);
			}
		});
	}

	// UARIV-202605 (ampliación): manejo de "Aprobar y Enviar" cuando hay varios usuarios con el
	// mismo orden configurado en el acto administrativo (bifurcación: el usuario elige a cuál enviar).
	$.handleSingComCheckResponse = function (response_value, actoadministrativo_id) {
		if (response_value.status == 200) {
			toastr.success(response_value.message);
			setTimeout(function () { document.location.reload(); }, 5000);
		} else if (response_value.status == 300 && response_value.candidatos) {
			$.mostrarSeleccionCandidatosFlujo(actoadministrativo_id, response_value.candidatos, response_value.message);
		} else {
			toastr.error(response_value.message);
		}
	};

	$.mostrarSeleccionCandidatosFlujo = function (actoadministrativo_id, candidatos, mensaje) {
		var htmlLista = '<div class="list-group">';
		jQuery.each(candidatos, function (index, candidato) {
			htmlLista += '<a href="#" class="list-group-item singcomcheck-candidato" data-actoadministrativo-id="' + actoadministrativo_id + '" data-usuario-id="' + candidato.usuario_id + '">' +
				'<strong>' + candidato.nombre + '</strong> <span class="text-muted">(' + candidato.rol + ')</span></a>';
		});
		htmlLista += '</div>';
		//*******************************************************************************************
		var $modal = jQuery('#modalSeleccionFlujo');
		if ($modal.length === 0) {
			jQuery('body').append(
				'<div class="modal fade" id="modalSeleccionFlujo" tabindex="-1" role="dialog">' +
				'<div class="modal-dialog" role="document"><div class="modal-content">' +
				'<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>' +
				'<h4 class="modal-title">Seleccionar destinatario</h4></div>' +
				'<div class="modal-body" id="modalSeleccionFlujoBody"></div>' +
				'</div></div></div>'
			);
			$modal = jQuery('#modalSeleccionFlujo');
		}
		jQuery('#modalSeleccionFlujoBody').html('<p>' + mensaje + '</p>' + htmlLista);
		$modal.modal('show');
	};

	jQuery('body').on('click', '.singcomcheck-candidato', function (e) {
		e.preventDefault();
		var actoadministrativo_id = jQuery(this).data('actoadministrativo-id');
		var usuario_destino_id = jQuery(this).data('usuario-id');
		jQuery('#modalSeleccionFlujo').modal('hide');
		jQuery.LoadingStructData();
		jQuery.ajax({
			url: 'acto_administrativo/singComCheck',
			type: 'POST',
			data: { actoadministrativo_id: actoadministrativo_id, usuario_destino_id: usuario_destino_id },
			complete: function (xhr) {
				jQuery.CloseLoadingStructData();
				try {
					var response_value = JSON.parse(xhr.responseText);
					if (response_value.status == 200) {
						toastr.success(response_value.message);
						setTimeout(function () { document.location.reload(); }, 3000);
					} else {
						toastr.error(response_value.message);
					}
				} catch (err) { toastr.error(err.message); }
			}
		});
	});
});

(function ($) {
	$.fn.currencyInput = function () {
		this.each(function () {
			var value_fact = $(this).val();
			if (value_fact == "") {
				return;
			}

			if (!$.isNumeric($(this).val())) {
				$(this).val('0').trigger('change');
			}

			var data_value = parseFloat($(this).val(), 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
			if (data_value == "") {
				$(this).val("");
			} else {
				$(this).val(data_value);
			}
		});
	};
})(jQuery);