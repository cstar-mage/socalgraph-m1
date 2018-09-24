function addMappingRow()
{
    // Remove the "no values found" row
    if($$('#claimGrid_table tbody tr input').length == 0) {
        $$('#claimGrid_table tbody').first().update();
    }

    var randomId = makeid();

    $$('#claimGrid_table tbody').first().insert(
        '<tr class="pointer">' +
        '<td>' +
        '<input type="text" name="external[]" class="text" style="width:300px;" placeholder="External Attribute">' +
        '&nbsp;&nbsp;<select name="transform[]" class="select" onchange="setInputBox(this, \'' + randomId + '\');">' +
        '<option value="string">Use value unmodified</option>' +
        '<option value="default">Default value on empty</option>' +
        '<option value="password">Magento Password Hash</option>' +
        '<option value="before">Substring: Before occurance</option>' +
        '<option value="after">Substring: After occurance</option>' +
        '<option value="preg">PERL regular expression</option>' +
        '</select>&nbsp;&nbsp;' +
        '<input type="text" name="extra[]" id="extra-' + randomId + '" class="text" style="display: none; width:200px;">' +
        '<br />' +
        '</td>' +
        '<td>' +
        '<select id="internal-' + randomId + '" name="internal[]" class="select"></select>' +
        '</td> ' +
        '<td class=" last"><a onclick="$(this).up(1).remove();" type="button">Delete Row</a>' +
        '</td>' +
        '</tr>');

    setEavMappings(randomId);
}

function setEavMappings(randomId)
{
    $$('#eav option').each(function(option, i) {
        var opt = document.createElement('option');
        opt.text = option.text;
        opt.value = option.value;

        $('internal-' + randomId).add(opt);
    });
}

function setInputBox(element, id)
{
    if($(element).value == 'preg') {
        $('extra-' + id).placeholder = '/^(\w+)/';
        $('extra-' + id).show();
    } else if($(element).value == 'before' || $(element).value == 'after') {
        $('extra-' + id).placeholder = 'Character';
        $('extra-' + id).show();
    } else if($(element).value == 'default') {
        $('extra-' + id).placeholder = 'Default value';
        $('extra-' + id).show();
    } else {
        $('extra-' + id).hide();
    }
}

function makeid()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}