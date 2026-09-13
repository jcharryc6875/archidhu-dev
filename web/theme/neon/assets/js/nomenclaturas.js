/**
 *	Estandarizador de direcciones
 *
 *	Developed by Javier Charry - javier.charry@gmail.com
 */

jQuery(document).ready(function(){    
    
    jQuery('#direccion').on('keyup', function (e) {
        jQuery("#direccion").val("");
        jQuery("#tviaprimaria").focus();
    });
    
    /*EVENTO DE CHANGE DEL SELECT VIA PRINCIPAL*/
    jQuery('#tviaprimaria').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);        
        var data_text = optionSelected.attr('data-text');
        var valueSelected = optionSelected.attr('value');
        if(valueSelected <= 0){
            resetAllValues();           
            return;
        }
        //*********************************************************************************      
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#dataViaPrincipal').on('keyup', function (e) {
        var optionSelected = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionSelected <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#letraViaPrincipal').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = optionSelected.attr('value')  != "0" ? optionSelected.attr('value') : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#bisViaPrincipal').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = optionSelected.attr('value')  != "0" ? optionSelected.attr('value') : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#letraBis').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = optionSelected.attr('value')  != "0" ? optionSelected.attr('value') : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    
    jQuery('#cuadranteViaPrincipal').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = optionSelected.attr('value')  != "0" ? optionSelected.attr('value') : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#numeroViaGeneradora').on('keyup', function (e) {
        var valueSelected =  jQuery('#numeroViaGeneradora').val().trim() != "" ? jQuery('#numeroViaGeneradora').val().trim() : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#letraViaGeneradora').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = optionSelected.attr('value')  != "0" ? optionSelected.attr('value') : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#numeroPlaca').on('keyup', function (e) {
        var valueSelected =  jQuery('#numeroPlaca').val().trim() != "" ? jQuery('#numeroPlaca').val().trim() : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************        
        jQuery("#direccion").val(getFullDireccionText());
    });
    
     jQuery('#cuadranteViaGeneradora').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = optionSelected.attr('value')  != "0" ? optionSelected.attr('value') : "";
        var optionViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value');
        if(optionViaPrincipal <= 0){
            resetAllValues();
            return;
        }
        //*********************************************************************************
        jQuery("#direccion").val(getFullDireccionText());
    });
    
    jQuery('#btnaddcomplemento').on('click', function (event) {
        event.preventDefault();
        var valueContainerComplemento = jQuery('#containercomplemento').find(":selected").attr('value');
        if(valueContainerComplemento <= 0){
            return;
        }
        //*********************************************************************************
        var textContainerComplemento = jQuery('#containercomplemento').find(":selected").attr('data-text');
        var text_complemento =  jQuery('#text_complmento').val().trim();
        //var data_direccion = getFullDireccionText().trim();
        var data_direccion = jQuery("#direccion").val().trim();
        var data_text = data_direccion != "" ? data_direccion + " " + textContainerComplemento + " " + text_complemento : textContainerComplemento + " " + text_complemento;
        jQuery("#direccion").val(data_text);
        //*********************************************************************************
        jQuery('#containercomplemento').val("0");
        jQuery('#text_complmento').val("");
    });
       
});    

function getFullDireccionText(){
    //*********************************************************************************
    var textViaPrincipal = jQuery('#tviaprimaria').find(":selected").attr('value') != "0" ? jQuery('#tviaprimaria').find(":selected").attr('data-text') + " " : "";
    var numViaPrincipal =  jQuery('#dataViaPrincipal').val().trim() ? jQuery('#dataViaPrincipal').val().trim() + "" : "";
    var letraViaPrincipal =  jQuery('#letraViaPrincipal').find(":selected").attr('value') != "0" ? jQuery('#letraViaPrincipal').find(":selected").attr('value') + " " : "";
    var bisViaPrincipal =  jQuery('#bisViaPrincipal').find(":selected").attr('value') != "0" ? jQuery('#bisViaPrincipal').find(":selected").attr('value') + " " : "";
    var letraBis =  jQuery('#letraBis').find(":selected").attr('value') != "0" ? jQuery('#letraBis').find(":selected").attr('value') + " " : "";
    var cuadranteViaPrincipal =  jQuery('#cuadranteViaPrincipal').find(":selected").attr('value') != "0" ? jQuery('#cuadranteViaPrincipal').find(":selected").attr('value') + " " : "";
    var numeroViaGeneradora =  jQuery('#numeroViaGeneradora').val().trim() ? " # " + jQuery('#numeroViaGeneradora').val().trim() : "";
    var letraViaGeneradora =  jQuery('#letraViaGeneradora').find(":selected").attr('value') != "0" ? jQuery('#letraViaGeneradora').find(":selected").attr('value') + "" : "";
    var numeroPlaca =  jQuery('#numeroPlaca').val().trim() ? "-" + jQuery('#numeroPlaca').val().trim() + " " : "";
    var cuadranteViaGeneradora =  jQuery('#cuadranteViaGeneradora').find(":selected").attr('value') != "0" ? jQuery('#cuadranteViaGeneradora').find(":selected").attr('value') : "";
    //*********************************************************************************
    var str_full = textViaPrincipal + numViaPrincipal + letraViaPrincipal + bisViaPrincipal + letraBis + cuadranteViaPrincipal + numeroViaGeneradora + letraViaGeneradora + numeroPlaca + cuadranteViaGeneradora;
    return str_full;
}

function resetAllValues(){
    jQuery("#direccion").val("");
    jQuery('#dataViaPrincipal').val("");
    jQuery('#letraViaPrincipal').val("0");
    jQuery('#bisViaPrincipal').val("0");
    jQuery('#letraBis').val("0");
    jQuery('#cuadranteViaPrincipal').val("0");
    jQuery('#numeroViaGeneradora').val("");
    jQuery('#letraViaGeneradora').val("0");
    jQuery('#numeroPlaca').val("");
    jQuery('#cuadranteViaGeneradora').val("0");
}