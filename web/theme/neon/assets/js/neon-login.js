/**
 *	Neon Login Script
 *
 *	Developed by Arlind Nushi - www.laborator.co
 */

 var neonLogin = neonLogin || {};

 ;(function($, window, undefined)
 {
	 "use strict";
	 
	 $(document).ready(function()
	 {
		 neonLogin.$container = $("#form_login");
		 
		 
		 // Login Form & Validation
		 neonLogin.$container.validate({
			 rules: {
				 username: {
					 required: true	
				 },
				 
				 password: {
					 required: true
				 },
				 
			 },
			 
			 highlight: function(element){
				 $(element).closest('.input-group').addClass('validate-has-error');
			 },
			 
			 
			 unhighlight: function(element)
			 {
				 $(element).closest('.input-group').removeClass('validate-has-error');
			 },
			 
			 submitHandler: function(ev)
			 {
				 /* 
					 Updated on v1.1.4
					 Login form now processes the login data, here is the file: data/sample-login-form.php
				 */
				 
				 //$(".login-page").addClass('logging-in'); // This will hide the login form and init the progress bar
				 $(".govco-form-signin").addClass('logging-in'); // This will hide the login form and init the progress bar
				 //$(".govco-form-signin").hide();
				 //$(".govco-form-signin").prop('disabled',true);
				 $(".govco-form-signin :submit").prop('disabled',true);
					 
				 // Hide Errors
				 $(".form-login-error").slideUp('fast');
				 $(".form-login-error").hide();
 
				 // We will wait till the transition ends				
				 setTimeout(function()
				 {
					 var random_pct = 25 + Math.round(Math.random() * 30);
					 
					 // The form data are subbmitted, we can forward the progress to 70%
					 //neonLogin.setPercentage(40 + random_pct);
					 //***************************************************************************************
					 // Send data to the server
					 $.ajax({
						 url: '/backend.php/security/login',
						 method: 'POST',
						 dataType: 'json',
						 data: {
							 username: $("input#username").val(),
							 password: $("input#password").val(),
							 postbackurl: $("input#urlpostback").val(),
						 },
						 error: function(response)
						 {
							 $.CloseLoadingStructData();
							 //console.log(response);
							 alert("Se ha presentado un problema("+response.statusText+"), intente de nuevo!");
							 window.location.href = '/backend.php/security/login';
						 },
						 beforeSend: function(){
							$.LoadingStructData('Autenticando el usuario...');
						 },
						 success: function(response)
						 {
							 // Login status [success|invalid]
							 var login_status = response.login_status;
															 
							 // Form is fully completed, we update the percentage
							 //neonLogin.setPercentage(100);
							 
							 
							 // We will give some time for the animation to finish, then execute the following procedures	
							 setTimeout(function()
							 {
								 // If login is invalid, we store the 
								 if(login_status == 'invalid')
								 {
									 $(".form-login-error p").text(response.error_mensaje);
									 $(".form-login-error").show();
									 $(".govco-form-signin").removeClass('logging-in');
									 $(".govco-form-signin :submit").prop('disabled',false);
									 $(".govco-form-signin :input[type='password']").val('');
									 $.CloseLoadingStructData();
									 //neonLogin.resetProgressBar(true);
								 }
								 else
								 if(login_status == 'success')
								 {
									 // Redirect to login page
									 setTimeout(function()
									 {
										 //console.log(response.redirect_url);
										 var redirect_url = baseurl;
										 if(response.redirect_url && response.redirect_url.length)
										 {
											 redirect_url += response.redirect_url;
										 }
										 window.location.href = redirect_url;
									 }, 400);
								 }
							 }, 1000);
						 }
					 });
				 }, 650);
			 }
		 });
		 
		 
		 // Login Form Setup
		 neonLogin.$body = $(".login-page");
		 neonLogin.$login_progressbar_indicator = $(".login-progressbar-indicator h3");
		 neonLogin.$login_progressbar = neonLogin.$body.find(".login-progressbar div");
		 
		 neonLogin.$login_progressbar_indicator.html('0%');
		 
		 if(neonLogin.$body.hasClass('login-form-fall'))
		 {
			 var focus_set = false;
			 
			 setTimeout(function(){ 
				 neonLogin.$body.addClass('login-form-fall-init')
				 
				 setTimeout(function()
				 {
					 if( !focus_set)
					 {
						 neonLogin.$container.find('input:first').focus();
						 focus_set = true;
					 }
					 
				 }, 550);
				 
			 }, 0);
		 }
		 else
		 {
			 neonLogin.$container.find('input:first').focus();
		 }
		 
		 // Focus Class
		 neonLogin.$container.find('.form-control').each(function(i, el)
		 {
			 var $this = $(el),
				 $group = $this.closest('.input-group');
			 
			 $this.prev('.input-group-addon').click(function()
			 {
				 $this.focus();
			 });
			 
			 $this.on({
				 focus: function()
				 {
					 $group.addClass('focused');
				 },
				 
				 blur: function()
				 {
					 $group.removeClass('focused');
				 }
			 });
		 });
		 
	 });
	 
 })(jQuery, window);