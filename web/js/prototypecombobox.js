var inner_select   = 'Select';
var value_select   = '0.5';
var loading_select = 'Loading';
var select_id      = 'c_id';
var select_value   = 'c_name';
 
function getJson(uri,id,param,next)
{
    new Ajax.Request(
        uri,
        {
            method: 'POST',                     
            parameters:
            {
                id: param
            },
            onLoading:
            function()
            {
                if ($(id) == '0.5')
                {
                    $(next).hide();
                }
                else
                {
                $(next).show();
                $(id).update('').disable();
                createOpt(id,value_select,loading_select);
                                }
            },
            onSuccess:
            function(json)
            {
                $(id).enable();
                json = json.responseText;
                getOpt(json.evalJSON(true),id);
            }
        });
}
 
function getOpt(json,id)
{
    $(id).update('');
    createOpt(id,value_select,inner_select);
    json.each(
        function(obj)
        {
            createOpt(id,obj[select_id],obj[select_value]);     
        }
    );
}
 
function createOpt(id,value,inner)
{
    var opt = document.createElement('option');
    opt.value = value;
    opt.innerHTML = inner;
    $(id).appendChild(opt);
}
 
function selectHide(id)
{
    $(id).hide();
}