/**
 *	Neon Main JavaScript File
 *
 *	Theme by: www.laborator.co
 **/

var public_vars = public_vars || {};

;(function($, window, undefined){

	"use strict";

	$(document).ready(function()
	{
        // Sidebar Menu var
		public_vars.$body	 	 	= $("body");
		public_vars.$pageContainer  = public_vars.$body.find(".page-container");
		public_vars.$chat 			= public_vars.$pageContainer.find('#chat');
		public_vars.$horizontalMenu = public_vars.$pageContainer.find('header.navbar');
		public_vars.$sidebarMenu	= public_vars.$pageContainer.find('.sidebar-menu');
		public_vars.$mainMenu	    = public_vars.$sidebarMenu.find('#main-menu');
		public_vars.$mainContent	= public_vars.$pageContainer.find('.main-content');
		public_vars.$sidebarUserEnv = public_vars.$sidebarMenu.find('.sidebar-user-info');
		public_vars.$sidebarUser 	= public_vars.$sidebarUserEnv.find('.user-link');
        
        $("#submitajax").click(function(e) 
		{
            var $valid = $( "#rootwizard" ).validate().form();
            if( ! $valid) { return false; }
			$.LoadingStructData();
            if ($(this).validate().form()) {
                e.preventDefault();
                $('#ajaxspinner').hide();
                $("#messagediv").empty();
                $('#submitajax').attr("disabled", true);
                if(checkForFiles())
				{
					postWithAjax(
						{
                      headers: createHeaderData(),
                      data : createMultipart(), 
                      cache: false,
                      contentType: false,
                      dataType: "json",
                      processData: false  
                    });
                } 
				else 
				{
                    postWithAjax({
                      headers : createHeaderData(),
                      data : createUrlData()
                    });    
                }
            }
        });
        
		$('.endradprocess').on('click', function(e) {
			$.LoadingStructData();
            window.location.href = '/oficina_virtual';			
        });

		$('.insearchprocess').on('click', function(e) {
			$.LoadingStructData();
            window.location.href = '/oficina_virtual/consulta';			
        });

        // Disable step
    	$('#ntipopeticionario_id').on('change', function() {
            if($('#ntipopeticionario_id').val() == 4)  //si es anonimo...
			{
				/**/
                $('.idatapry').hide();
                $('#email').rules( 'remove', 'required' );
                $('#email').removeClass('required validate-has-error').addClass('valid');

				$('#ntipoidentificacion_id').rules( 'remove', 'required' );
                $('#ntipoidentificacion_id').removeClass('required validate-has-error').addClass('valid');
				$('#num_identificacion').rules( 'remove', 'required' );
                $('#num_identificacion').removeClass('required validate-has-error').addClass('valid');
				$('#primer_nombre').rules( 'remove', 'required' );
                $('#primer_nombre').removeClass('required validate-has-error').addClass('valid');
				$('#primer_apellido').rules( 'remove', 'required' );
                $('#primer_apellido').removeClass('required validate-has-error').addClass('valid'); 
				$('#ndireccion').rules( 'remove', 'required' );
                $('#ndireccion').removeClass('required validate-has-error').addClass('valid');   
				
                //$('#rootwizard').bootstrapWizard('remove', 1);

			}
			else
			{
                $('.idatapry').show();
                $('#email').rules( "add", { required: true });
                $('#email').addClass('required validate-has-error').removeClass('valid');
                //$('#rootwizard').bootstrapWizard('display', 1);
            }
    	});
                 
        $("#npais_id").on('change', function(ev){
            // Send data to the server
            ev.preventDefault();
			$.ajax({
				url: qudep + '&item=' + $("select#npais_id").val(),
				method: 'GET',
				dataType: 'json',
				data: { },
                beforeSend: function( xhr ) {
                    $( "#ndepartamento_id" ).prop( "disabled", true );
                },
				error: function(response)
				{
				    //console.log(response);
                    $('#ndepartamento_id').empty().append('<option value="">Seleccione...</option>');
					alert("Se ha presentado un problema("+response.statusText+"), intente de nuevo!");
				},
				success: function(response)
				{
                    var items = jQuery.parseJSON( response.object );
                    var ndepartamento = $('#ndepartamento_id');
                    ndepartamento.empty().append('<option value="">Seleccione...</option>');
                    $.each(items,function(key, value) {
                        ndepartamento.append('<option value="' + key + '">' + value + '</option>');
                    });
				},
                complete: function()
                {
                    $('#ndepartamento_id').val('');
                    $( "#ndepartamento_id" ).prop( "disabled", false );
                }
			});
        });
        
        $("#ndepartamento_id").on('change', function(ev){
            // Send data to the server
            ev.preventDefault();
			$.ajax({
				url: quciudad + '&item=' + $("select#ndepartamento_id").val(),
				method: 'GET',
				dataType: 'json',
				data: { },
                beforeSend: function( xhr ) {
                    $( "#nciudad_id" ).prop( "disabled", true );
                },
				error: function(response)
				{
				    //console.log(response);
                    $('#nciudad_id').empty().append('<option value="">Seleccione...</option>');
					alert("Se ha presentado un problema("+response.statusText+"), intente de nuevo!");
				},
				success: function(response)
				{
                    var items = jQuery.parseJSON( response.object );
                    var nciudad = $('#nciudad_id');
                    nciudad.empty().append('<option value="">Seleccione...</option>');
                    $.each(items,function(key, value) {
                        nciudad.append('<option value="' + key + '">' + value + '</option>');
                    });
				},
                complete: function()
                {
                    $('#nciudad_id').val('');
                    $( "#nciudad_id" ).prop( "disabled", false );
                }
			});
        });
        
		if($.isFunction($.fn.select2))
		{
			$(".select2").each(function(i, el)
			{
				var $this = $(el),
					opts = {
                        allowClear: attrDefault($this, 'allowClear', true),
                        allowClear: true,
                        //minimumInputLength: 2
                        //tags: true,
                        //placeholder: "Select an attribute"
					};

				$this.select2(opts);
				$this.addClass('visible');

				//$this.select2("open");
			});


			if($.isFunction($.fn.niceScroll))
			{
				$(".select2-results").niceScroll({
					cursorcolor: '#d4d4d4',
					cursorborder: '1px solid #ccc',
					railpadding: {right: 3}
				});
			}
		}        
        
        // Select2 Dropdown replacement
		/*if($.isFunction($.fn.select2))
		{
			$(".select2").each(function(i, el)
			{
				var $this = $(el),
					opts = {
						allowClear: attrDefault($this, 'allowClear', false)
					};

				$this.select2(opts);
				$this.addClass('visible');

				//$this.select2("open");
			});


			if($.isFunction($.fn.niceScroll))
			{
				$(".select2-results").niceScroll({
					cursorcolor: '#d4d4d4',
					cursorborder: '1px solid #ccc',
					railpadding: {right: 3}
				});
			}
		}*/
        
		// Datepicker
		if($.isFunction($.fn.datepicker))
		{
			$(".datepicker").each(function(i, el)
			{
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

				if($n.is('.input-group-addon') && $n.has('a'))
				{
					$n.on('click', function(ev)
					{
						ev.preventDefault();

						$this.datepicker('show');
					});
				}

				if($p.is('.input-group-addon') && $p.has('a'))
				{
					$p.on('click', function(ev)
					{
						ev.preventDefault();

						$this.datepicker('show');
					});
				}
			});
		}

		// Input Mask
		if($.isFunction($.fn.inputmask))
		{
			$("[data-mask]").each(function(i, el)
			{
				var $this = $(el),
					mask = $this.data('mask').toString(),
					opts = {
						numericInput: attrDefault($this, 'numeric', false),
						radixPoint: attrDefault($this, 'radixPoint', ''),
						rightAlignNumerics: attrDefault($this, 'numericAlign', 'left') == 'right'
					},
					placeholder = attrDefault($this, 'placeholder', ''),
					is_regex = attrDefault($this, 'isRegex', '');


				if(placeholder.length)
				{
					opts[placeholder] = placeholder;
				}

				switch(mask.toLowerCase())
				{
					case "phone":
						mask = "(999) 999-9999";
						break;

					case "currency":
					case "rcurrency":

						var sign = attrDefault($this, 'sign', '$');;

						mask = "999,999,999.99";

						if($this.data('mask').toLowerCase() == 'rcurrency')
						{
							mask += ' ' + sign;
						}
						else
						{
							mask = sign + ' ' + mask;
						}

						opts.numericInput = true;
						opts.rightAlignNumerics = false;
						opts.radixPoint = '.';
						break;

					case "email":
						mask = 'Regex';
						opts.regex = "[a-zA-Z0-9._%-]+@[a-zA-Z0-9-]+\\.[a-zA-Z]{2,4}";
						break;

					case "fdecimal":
						mask = 'decimal';
						$.extend(opts, {
							autoGroup		: true,
							groupSize		: 3,
							radixPoint		: attrDefault($this, 'rad', '.'),
							groupSeparator	: attrDefault($this, 'dec', ',')
						});
				}

				if(is_regex)
				{
					opts.regex = mask;
					mask = 'Regex';
				}

				$this.inputmask(mask, opts);
			});
		}

        // Form Wizard
		if($.isFunction($.fn.bootstrapWizard))
		{
			$(".form-wizard").each(function(i, el)
			{
				var $this = $(el),
					$progress = $this.find(".steps-progress div"),
					_index = $this.find('> ul > li.active').index();

				// Validation
				var checkFormWizardValidaion = function(tab, navigation, index)
					{
			  			if($this.hasClass('validate'))
			  			{
							var $valid = $this.valid();

							if( ! $valid)
							{
								$this.data('validator').focusInvalid();
								return false;
							}
						}

				  		return true;
					};


				$this.bootstrapWizard({
					tabClass: "",
			  		onTabShow: function($tab, $navigation, index)
			  		{

						setCurrentProgressTab($this, $navigation, $tab, $progress, index);
			  		},

			  		onNext: checkFormWizardValidaion,
			  		onTabClick: checkFormWizardValidaion
			  	});

			  	$this.data('bootstrapWizard').show( _index );

			  	/*$(window).on('neon.resize', function()
			  	{
			  		$this.data('bootstrapWizard').show( _index );
			  	});*/
			});
		}


		// Form Validation
		if($.isFunction($.fn.validate))
		{
			$("form.validate").each(function(i, el)
			{
				var $this = $(el),
					opts = {
						rules: {},
						messages: {},
                        //ignore: [],
						errorElement: 'span',
						errorClass: 'validate-has-error',
						highlight: function( element, errorClass, validClass ) {
                            if ( element.type === "radio" ) {
                                this.findByName( element.name ).addClass( errorClass ).removeClass( validClass );
                            } else {
                                var elem = $(element);
                                if (elem.attr('readonly') == 'readonly') {
                                    if (elem.hasClass("input-group-addon")) {
                                       $("#" + elem.attr("id")).parent().addClass(errorClass);
                                    } else {
                                        $( element ).addClass( errorClass ).removeClass( validClass );
                                    }
                                } else {
                                    if (elem.hasClass("select2-hidden-accessible")) {
                                       $("#select2-" + elem.attr("id") + "-container").parent().addClass(errorClass);
                                    } else {
                                        $( element ).addClass( errorClass ).removeClass( validClass );
                                    }
                                }
                            }
                        },
                        unhighlight: function( element, errorClass, validClass ) {
                            if ( element.type === "radio" ) {
                                this.findByName( element.name ).removeClass( errorClass ).addClass( validClass );
                            } else {
                                var elem = $(element);
                                if (elem.attr('readonly') == 'readonly') {
                                    if (elem.hasClass("input-group-addon")) {
                                       $("#" + elem.attr("id")).parent().removeClass(errorClass);
                                    } else {
                                        $( element ).addClass( errorClass ).removeClass( validClass );
                                    }
                                } else {
                                    if (elem.hasClass("select2-hidden-accessible")) {
                                        $("#select2-" + elem.attr("id") + "-container").parent().removeClass(errorClass);
                                    } else {
                                        $( element ).removeClass( errorClass ).addClass( validClass );
                                    }
                                }
                            }
                        },
                        errorPlacement: function(error, element) {
                            var elem = $(element);
                            if (elem.attr('readonly') == 'readonly') {
                                element = $("#" + elem.attr("id")).parent();
                                error.insertAfter(element);
                            } else {
                                if (elem.hasClass("select2-hidden-accessible")) {
                                    element = $("#select2-" + elem.attr("id") + "-container").parent().parent().parent();
                                    error.insertAfter(element);
                                } else {
                                    error.insertAfter(element);
                                }
                            }
                        }
					},
					$fields = $this.find('[data-validate]');


				$fields.each(function(j, el2)
				{
					var $field = $(el2),
						name = $field.attr('name'),
						validate = attrDefault($field, 'validate', '').toString(),
						_validate = validate.split(',');

					for(var k in _validate)
					{
						var rule = _validate[k],
							params,
							message;

						if(typeof opts['rules'][name] == 'undefined')
						{
							opts['rules'][name] = {};
							opts['messages'][name] = {};
						}

						if($.inArray(rule, ['required', 'url', 'email', 'number', 'date', 'creditcard']) != -1)
						{
							opts['rules'][name][rule] = true;

							message = $field.data('message-' + rule);

							if(message)
							{
								opts['messages'][name][rule] = message;
							}
						}
						// Parameter Value (#1 parameter)
						else
						if(params = rule.match(/(\w+)\[(.*?)\]/i))
						{
							if($.inArray(params[1], ['min', 'max', 'minlength', 'maxlength', 'equalTo']) != -1)
							{
								opts['rules'][name][params[1]] = params[2];


								message = $field.data('message-' + params[1]);

								if(message)
								{
									opts['messages'][name][params[1]] = message;
								}
							}
						}
					}
				});

				//console.log( opts );
				$this.validate(opts);
			});
		}
	});
})(jQuery, window);


/* Functions */

// Sidebar Menu Setup
function setup_sidebar_menu()
{
	var $ = jQuery,
		$items_with_submenu	  = public_vars.$sidebarMenu.find('li:has(ul)'),
		submenu_options		  = {
			submenu_open_delay: 0.25,
			submenu_open_easing: Sine.easeInOut,
			submenu_opened_class: 'opened'
		},
		root_level_class 	  = 'root-level',
		is_multiopen 		  = public_vars.$mainMenu.hasClass('multiple-expanded');

	public_vars.$mainMenu.find('> li').addClass(root_level_class);

	$items_with_submenu.each(function(i, el)
	{
		var $this = $(el),
			$link = $this.find('> a'),
			$submenu = $this.find('> ul');

		$this.addClass('has-sub');

		$link.click(function(ev)
		{
			ev.preventDefault();

			if( ! is_multiopen && $this.hasClass(root_level_class))
			{
				var close_submenus = public_vars.$mainMenu.find('.' + root_level_class).not($this).find('> ul');

				close_submenus.each(function(i, el)
				{
					var $sub = $(el);
					menu_do_collapse($sub, $sub.parent(), submenu_options);
				});
			}

			if( ! $this.hasClass(submenu_options.submenu_opened_class))
			{
				var current_height;

				if( ! $submenu.is(':visible'))
				{
					menu_do_expand($submenu, $this, submenu_options);
				}
			}
			else
			{
				menu_do_collapse($submenu, $this, submenu_options);
			}
		});

	});

	// Open the submenus with "opened" class
	public_vars.$mainMenu.find('.'+submenu_options.submenu_opened_class+' > ul').addClass('visible');

	// Well, somebody may forgot to add "active" for all inhertiance, but we are going to help you (just in case) - we do this job for you for free :P!
	if(public_vars.$mainMenu.hasClass('auto-inherit-active-class'))
	{
		menu_set_active_class_to_parents( public_vars.$mainMenu.find('.active') );
	}

	// Search Input
	var $search_input = public_vars.$mainMenu.find('#search input[type="text"]'),
		$search_el = public_vars.$mainMenu.find('#search');

	public_vars.$mainMenu.find('#search form').submit(function(ev)
	{
		var is_collapsed = public_vars.$pageContainer.hasClass('sidebar-collapsed');

		if(is_collapsed)
		{
			if($search_el.hasClass('focused') == false)
			{
				ev.preventDefault();
				$search_el.addClass('focused');

				$search_input.focus();

				return false;
			}
		}
	});

	$search_input.on('blur', function(ev)
	{
		var is_collapsed = public_vars.$pageContainer.hasClass('sidebar-collapsed');

		if(is_collapsed)
		{
			$search_el.removeClass('focused');
		}
	});
}


function menu_do_expand($submenu, $this, options)
{
	$submenu.addClass('visible').height('');
	current_height = $submenu.outerHeight();

	var props_from = {
		opacity: .2,
		height: 0,
		top: -20
	},
	props_to = {
		height: current_height,
		opacity: 1,
		top: 0
	};

	if(isxs())
	{
		delete props_from['opacity'];
		delete props_from['top'];

		delete props_to['opacity'];
		delete props_to['top'];
	}

	TweenMax.set($submenu, {css: props_from});

	$this.addClass(options.submenu_opened_class);

	TweenMax.to($submenu, options.submenu_open_delay, {css: props_to, ease: options.submenu_open_easing, onUpdate: ps_update, onComplete: function()
	{
		$submenu.attr('style', '');
	}});
}


function menu_do_collapse($submenu, $this, options)
{
	if(public_vars.$pageContainer.hasClass('sidebar-collapsed') && $this.hasClass('root-level'))
	{
		return;
	}

	$this.removeClass(options.submenu_opened_class);

	TweenMax.to($submenu, options.submenu_open_delay, {css: {height: 0, opacity: .2}, ease: options.submenu_open_easing, onUpdate: ps_update, onComplete: function()
	{
		$submenu.removeClass('visible');
	}});
}


function menu_set_active_class_to_parents($active_element)
{
	if($active_element.length)
	{
		var $parent = $active_element.parent().parent();

		$parent.addClass('active');

		if(! $parent.hasClass('root-level'))
			menu_set_active_class_to_parents($parent)
	}
}



// Horizontal Menu Setup
function setup_horizontal_menu()
{
	var $					  = jQuery,
		$nav_bar_menu		  = public_vars.$horizontalMenu.find('.navbar-nav'),
		$items_with_submenu	  = $nav_bar_menu.find('li:has(ul)'),
		$search				  = public_vars.$horizontalMenu.find('li#search'),
		$search_input		  = $search.find('.search-input'),
		$search_submit		  = $search.find('form'),
		root_level_class 	  = 'root-level'
		is_multiopen 		  = $nav_bar_menu.hasClass('multiple-expanded'),
		submenu_options		  = {
			submenu_open_delay: 0.5,
			submenu_open_easing: Sine.easeInOut,
			submenu_opened_class: 'opened'
		};

	$nav_bar_menu.find('> li').addClass(root_level_class);

	$items_with_submenu.each(function(i, el)
	{
		var $this = $(el),
			$link = $this.find('> a'),
			$submenu = $this.find('> ul');

		$this.addClass('has-sub');

		setup_horizontal_menu_hover($this, $submenu);

		// xs devices only
		$link.click(function(ev)
		{
			if(isxs())
			{
				ev.preventDefault();

				if( ! is_multiopen && $this.hasClass(root_level_class))
				{
					var close_submenus = $nav_bar_menu.find('.' + root_level_class).not($this).find('> ul');

					close_submenus.each(function(i, el)
					{
						var $sub = $(el);
						menu_do_collapse($sub, $sub.parent(), submenu_options);
					});
				}

				if( ! $this.hasClass(submenu_options.submenu_opened_class))
				{
					var current_height;

					if( ! $submenu.is(':visible'))
					{
						menu_do_expand($submenu, $this, submenu_options);
					}
				}
				else
				{
					menu_do_collapse($submenu, $this, submenu_options);
				}
			}
		});

	});


	// Search Input
	if($search.hasClass('search-input-collapsed'))
	{
		$search_submit.submit(function(ev)
		{
			if($search.hasClass('search-input-collapsed'))
			{
				ev.preventDefault();
				$search.removeClass('search-input-collapsed');
				$search_input.focus();

				return false;
			}
		});

		$search_input.on('blur', function(ev)
		{
			$search.addClass('search-input-collapsed');
		});
	}
}

jQuery(public_vars, {
	hover_index: 4
});

function setup_horizontal_menu_hover($item, $sub)
{
	var del = 0.5,
		trans_x = -10,
		ease = Quad.easeInOut;

	TweenMax.set($sub, {css: {autoAlpha: 0, transform: "translateX("+trans_x+"px)"}});

	$item.hoverIntent({
		over: function()
		{
			if(isxs())
				return false;

			if($sub.css('display') == 'none')
			{
				$sub.css({display: 'block', visibility: 'hidden'});
			}

			$sub.css({zIndex: ++public_vars.hover_index});
			TweenMax.to($sub, del, {css: {autoAlpha: 1, transform: "translateX(0px)"}, ease: ease});
		},

		out: function()
		{
			if(isxs())
				return false;

			TweenMax.to($sub, del, {css: {autoAlpha: 0, transform: "translateX("+trans_x+"px)"}, ease: ease, onComplete: function()
			{
				TweenMax.set($sub, {css: {transform: "translateX("+trans_x+"px)"}});
				$sub.css({display: 'none'});
			}});
		},

		timeout: 300,
		interval: 50
	});

}



// Block UI Helper
function blockUI($el)
{
	$el.block({
		message: '',
		css: {
			border: 'none',
			padding: '0px',
			backgroundColor: 'none'
		},
		overlayCSS: {
			backgroundColor: '#fff',
			opacity: .3,
			cursor: 'wait'
		}
	});
}

function unblockUI($el)
{
	$el.unblock();
}


// Element Attribute Helper
function attrDefault($el, data_var, default_val)
{
	if(typeof $el.data(data_var) != 'undefined')
	{
		return $el.data(data_var);
	}

	return default_val;
}



// Test function
function callback_test()
{
	alert("Callback function executed! No. of arguments: " + arguments.length + "\n\nSee console log for outputed of the arguments.");

	console.log(arguments);
}


// Root Wizard Current Tab
function setCurrentProgressTab($rootwizard, $nav, $tab, $progress, index)
{
	$tab.prevAll().addClass('completed');
	$tab.nextAll().removeClass('completed');

	var items      	  = $nav.children().length,
		pct           = parseInt((index+1) / items * 100, 10),
		$first_tab    = $nav.find('li:first-child'),
		margin        = (1/(items*2) * 100) + '%';//$first_tab.find('span').position().left + 'px';

	if( $first_tab.hasClass('active'))
	{
		$progress.width(0);
	}
	else
	{
		if(rtl())
		{
			$progress.width( $progress.parent().outerWidth(true) - $tab.prev().position().left - $tab.find('span').width()/2 );
		}
		else
		{
			$progress.width( ((index-1) /(items-1)) * 100 + '%' ); //$progress.width( $tab.prev().position().left - $tab.find('span').width()/2 );
		}
	}


	$progress.parent().css({
		marginLeft: margin,
		marginRight: margin
	});

	/*var m = $first_tab.find('span').position().left - $first_tab.find('span').width() / 2;

	$rootwizard.find('.tab-content').css({
		marginLeft: m,
		marginRight: m
	});*/
}


// Replace Checkboxes
function replaceCheckboxes()
{
	var $ = jQuery;

	$(".checkbox-replace:not(.neon-cb-replacement), .radio-replace:not(.neon-cb-replacement)").each(function(i, el)
	{
		var $this = $(el),
			$input = $this.find('input:first'),
			$wrapper = $('<label class="cb-wrapper" />'),
			$checked = $('<div class="checked" />'),
			checked_class = 'checked',
			is_radio = $input.is('[type="radio"]'),
			$related,
			name = $input.attr('name');


		$this.addClass('neon-cb-replacement');


		$input.wrap($wrapper);

		$wrapper = $input.parent();

		$wrapper.append($checked).next('label').on('click', function(ev)
		{
			$wrapper.click();
		});

		$input.on('change', function(ev)
		{
			if(is_radio)
			{
				//$(".neon-cb-replacement input[type=radio][name='"+name+"']").closest('.neon-cb-replacement').removeClass(checked_class);
				$(".neon-cb-replacement input[type=radio][name='"+name+"']:not(:checked)").closest('.neon-cb-replacement').removeClass(checked_class);
			}

			if($input.is(':disabled'))
			{
				$wrapper.addClass('disabled');
			}

			$this[$input.is(':checked') ? 'addClass' : 'removeClass'](checked_class);

		}).trigger('change');
	});
}



// Scroll to Bottom
function scrollToBottom($el)
{
	var $ = jQuery;

	if(typeof $el == 'string')
		$el = $($el);

	$el.get(0).scrollTop = $el.get(0).scrollHeight;
}


// Check viewport visibility (entrie element)
function elementInViewport(el)
{
	var top = el.offsetTop;
	var left = el.offsetLeft;
	var width = el.offsetWidth;
	var height = el.offsetHeight;

	while (el.offsetParent) {
		el = el.offsetParent;
		top += el.offsetTop;
		left += el.offsetLeft;
	}

	return (
		top >= window.pageYOffset &&
		left >= window.pageXOffset &&
		(top + height) <= (window.pageYOffset + window.innerHeight) &&
		(left + width) <= (window.pageXOffset + window.innerWidth)
	);
}

// X Overflow
function disableXOverflow()
{
	public_vars.$body.addClass('overflow-x-disabled');
}

function enableXOverflow()
{
	public_vars.$body.removeClass('overflow-x-disabled');
}


// Page Transitions
function init_page_transitions()
{
	var transitions = ['page-fade', 'page-left-in', 'page-right-in', 'page-fade-only'];

	for(var i in transitions)
	{
		var transition_name = transitions[i];

		if(public_vars.$body.hasClass(transition_name))
		{
			public_vars.$body.addClass(transition_name + '-init')

			setTimeout(function()
			{
				public_vars.$body.removeClass(transition_name + ' ' + transition_name + '-init');

			}, 850);

			return;
		}
	}
}


// Page Visibility API
function onPageAppear(callback)
{

	var hidden, state, visibilityChange;

	if (typeof document.hidden !== "undefined")
	{
		hidden = "hidden";
		visibilityChange = "visibilitychange";
		state = "visibilityState";
	}
	else if (typeof document.mozHidden !== "undefined")
	{
		hidden = "mozHidden";
		visibilityChange = "mozvisibilitychange";
		state = "mozVisibilityState";
	}
	else if (typeof document.msHidden !== "undefined")
	{
		hidden = "msHidden";
		visibilityChange = "msvisibilitychange";
		state = "msVisibilityState";
	}
	else if (typeof document.webkitHidden !== "undefined")
	{
		hidden = "webkitHidden";
		visibilityChange = "webkitvisibilitychange";
		state = "webkitVisibilityState";
	}

	if(document[state] || typeof document[state] == 'undefined')
	{
		callback();
	}

	document.addEventListener(visibilityChange, callback, false);
}


function continueWrappingPanelTables()
{
	var $tables = jQuery(".panel-body.with-table + table");

	if($tables.length)
	{
		$tables.wrap('<div class="panel-body with-table"></div>');
		continueWrappingPanelTables();
	}
}

// Root Wizard Current Tab
function setCurrentProgressTab($rootwizard, $nav, $tab, $progress, index)
{
	$tab.prevAll().addClass('completed');
	$tab.nextAll().removeClass('completed');

	var items      	  = $nav.children().length,
		pct           = parseInt((index+1) / items * 100, 10),
		$first_tab    = $nav.find('li:first-child'),
		margin        = (1/(items*2) * 100) + '%';//$first_tab.find('span').position().left + 'px';

	if( $first_tab.hasClass('active'))
	{
		$progress.width(0);
	}
	else
	{
		if(rtl())
		{
			$progress.width( $progress.parent().outerWidth(true) - $tab.prev().position().left - $tab.find('span').width()/2 );
		}
		else
		{
			$progress.width( ((index-1) /(items-1)) * 100 + '%' ); //$progress.width( $tab.prev().position().left - $tab.find('span').width()/2 );
		}
	}


	$progress.parent().css({
		marginLeft: margin,
		marginRight: margin
	});

	/*var m = $first_tab.find('span').position().left - $first_tab.find('span').width() / 2;

	$rootwizard.find('.tab-content').css({
		marginLeft: m,
		marginRight: m
	});*/
}

function show_loading_bar(options)
{
	var defaults = {
		pct: 0,
		delay: 1.3,
		wait: 0,
		before: function(){},
		finish: function(){},
		resetOnEnd: true
	};

	if(typeof options == 'object')
		defaults = jQuery.extend(defaults, options);
	else
	if(typeof options == 'number')
		defaults.pct = options;


	if(defaults.pct > 100)
		defaults.pct = 100;
	else
	if(defaults.pct < 0)
		defaults.pct = 0;

	var $ = jQuery,
		$loading_bar = $(".neon-loading-bar");

	if($loading_bar.length == 0)
	{
		$loading_bar = $('<div class="neon-loading-bar progress-is-hidden"><span data-pct="0"></span></div>');
		public_vars.$body.append( $loading_bar );
	}

	var $pct = $loading_bar.find('span'),
		current_pct = $pct.data('pct'),
		is_regress = current_pct > defaults.pct;


	defaults.before(current_pct);

	TweenMax.to($pct, defaults.delay, {css: {width: defaults.pct + '%'}, delay: defaults.wait, ease: is_regress ? Expo.easeOut : Expo.easeIn,
	onStart: function()
	{
		$loading_bar.removeClass('progress-is-hidden');
	},
	onComplete: function()
	{
		var pct = $pct.data('pct');

		if(pct == 100 && defaults.resetOnEnd)
		{
			hide_loading_bar();
		}

		defaults.finish(pct);
	},
	onUpdate: function()
	{
		$pct.data('pct', parseInt($pct.get(0).style.width, 10));
	}});
}

function hide_loading_bar()
{
    var $ = jQuery,
    	$loading_bar = $(".neon-loading-bar"),
    	$pct = $loading_bar.find('span');
    
    $loading_bar.addClass('progress-is-hidden');
    $pct.width(0).data('pct', 0);
}

function httpMessage(messageTxt, alertClass) {
	jQuery("#messagediv").append('<div class="alert '+alertClass+'"> <a class="close" data-dismiss="alert">&times;</a>'+messageTxt+'</div>');
}

function postWithAjax(myajax) {
    myajax = myajax || {};
    myajax.url = jQuery("#urlapi").val();
    myajax.type = "POST";
    //*********************************************************************
    myajax.complete = function(jqXHR) {
		//debugger;
		jQuery.CloseLoadingStructData();
		if (jqXHR.status == 0) {
			httpMessage("HTTP " + jqXHR.status + " " + jqXHR.statusText,"alert-danger");
		} else if (jqXHR.status >= 200 && jqXHR.status < 300) {
			jQuery("#messagediv").removeClass("alert-danger");
			httpMessage(jqXHR.responseText,"alert-success");
			//***************************************************************
			//debugger;
			var data_result = jQuery.parseJSON( jqXHR.responseText );
			//console.log(data_result);
			var windowHeight = jQuery(window).height();
			var windowWidth = jQuery(window).width();
			var boxHeight = jQuery('#modal-4').height();
			var boxWidth = jQuery('#modal-4').width();			

			jQuery( "#modal-4 div.modal-body" ).append( "<p>Radicado: " + data_result.object.radicado + "</p>" );
			jQuery( "#modal-4 div.modal-body" ).append( "<p>Asunto: " + data_result.object.asunto_com + "</p>" );
			jQuery( "#modal-4 div.modal-body" ).append( "<p>Fecha Creaci&oacute;n: " + data_result.object.fecha_creacion + "</p>" );
			jQuery('#modal-4').css({'left' : ((windowWidth - boxWidth)/2), 'top' : ((windowHeight - boxHeight)/2)});
			jQuery('#modal-4').modal('show', {backdrop: 'static'});
		} else if (jqXHR.status >= 400) {
			//debugger;
			jQuery.CloseLoadingStructData();
		  	jQuery("#messagediv").removeClass("alert-success").addClass("alert-danger");
          	httpMessage("HTTP " + jqXHR.status + " " + jqXHR.statusText,"alert-danger");
		} else {
			jQuery.CloseLoadingStructData();
			jQuery("#messagediv").removeClass("alert-danger alert-success");
			httpMessage("HTTP " + jqXHR.status + " " + jqXHR.statusText,"alert-warning");
		}		
	}
    //*********************************************************************
    /*myajax.success = function(data, textStatus, jqXHR) {
        console.log(data);
    }*/
    /*var req = jQuery.ajax(myajax).success(function(data, textStatus, jqXHR){
        console.log(data);
	});*/
    //*********************************************************************
	if (jQuery.isEmptyObject(myajax.data)) {
		myajax.contentType = 'application/x-www-form-urlencoded';
	}
    //*********************************************************************
	//jQuery("#messagediv").hide();
    //*********************************************************************
    jQuery('#ajaxspinner').show();
	var req = jQuery.ajax(myajax).always(function(){
        jQuery('#ajaxspinner').hide();
        jQuery('#submitajax').attr("disabled", false);
	});
}

function createHeaderData(){
    var mydata = {};
	var parameters = jQuery("#allheaders").find(".realinputvalue");
	for (i = 0; i < parameters.length; i++) {
		name = jQuery(parameters).eq(i).attr("name");
		if (name == undefined || name == "undefined") {
			continue;
		}
		value = jQuery(parameters).eq(i).val();
		mydata[name] = value
	}
    return(mydata);
}

function createMultipart(){
    //create multipart object
    var data = new FormData();  
    //add parameters
    var parameters = jQuery("#rootwizard").find(".realinputvalue");
	
	for (i = 0; i < parameters.length; i++) {
		name = jQuery(parameters).eq(i).attr("name");
		if (name == undefined || name == "undefined") {
			continue;
		}
        if(parameters[i].files){
      	  data.append(name, parameters[i].files[0]);      
        } else {
    		  data.append(name, jQuery(parameters).eq(i).val());
        }
	}
    return(data)  
}

function checkForFiles() {
	return jQuery("#rootwizard").find(".input-file").length > 0;
}