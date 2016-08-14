/*function shippingValue(idCountry)
 {
 $("#shippingChoices").html('');
 $("#shippingCost").html('');
 $("#totalprice").html(formatNumber(initPrice)+'€');
 var request = $.ajax({
 url: '{#{{  path('dywee_shipmentMethod_search_ajax') }}#}',
 type: 'post',
 dataType: 'json',
 data: {country: idCountry},
 success: function(data, textStatus, jqXHR){
 if(data.type == 'success') {
 $.each( data.shippingOptions, function( key, optionElement ) {
 var html = '<div class="checkbox">';
 html += '<label>';
 html += '<input type="radio" name="shippingOptions" value="' + optionElement.id + '" aria-price="' + optionElement.price + '" onclick="updatePrice(this)"> ';
 html += optionElement.name + ' (';

 if('priceByShipment' in optionElement)
 html += optionElement.priceByShipment + '€ par envoi';
 else html += optionElement.price + '€';
 html += ')</label></div>';
 $("#shippingChoices").append(html);
 });
 }
 else {
 //alert(data.text);
 }
 }
 });

 request.fail(function(jqXHR, textStatus) {
 ('#log').html('Request failed: ' + textStatus);
 });
 }*/

$(document).ready(function(){
    /*$(".removeOne").on('click', function(e){
        console.log('remove');
        e.preventDefault();
        let val = parseInt($(".basket-control").val()) - 1;
        if(val < 0)
            val = 0;
        $(".basket-control").data('quantity', val).val(val);
        processAjax($(this));
    });

    $(".addOne").on('click', function(e){
        console.log('add');
        e.preventDefault();
        addProduct($(this).data('product'));
        let val = parseInt($(".basket-control").val()) + 1;
        $(".basket-control").data('quantity', val).val(val);
        processAjax($(this).attr('href'));
    });*/

    function updatePrice(radio)
    {
        var price = $(radio).attr('aria-price');
        $("#shippingCost").html(price+'€');
        var totalPrice = initPrice + parseFloat(price);
        $("#totalprice").html(formatNumber(totalPrice)+'€');
    }

    function formatNumber(number)
    {
        var number = number.toFixed(2) + '';
        var x = number.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        return x1 + x2;
    }

    $(document).ready(function(){
        //shippingValue(1);
    });

    $("#form_country").change(function(){
        //shippingValue($(this).val());
    });

    var modify = [];

    function addProduct(id)
    {
        return modifyQuantity(id, 1);
    }

    function removeProduct(id)
    {
        return modifyQuantity(id, -1);
    }

    //Checker le nom pour modification
    $('.basket-control').on('keyDown', function(){
        if($(this).attr('quantity') != $(this).val())
        {
            $parent = $(this).parent().parent();
            $parent.find('.quantity-control').hide();
            $parent.find('.quantity-validator').show();
        }
    });


    $('.basket-control').on('blur', function(){

    });


    function modifyQuantity(id, quantity)
    {
        console.log('ici');
        $.ajax({
            url: Routing.generate('basket_add_product', {id: id, quantity: quantity}),
            method: "post",
            data: {}
        }).done(function(data){
            if(data.type == 'success')
            {

            }
            else console.log('[BASKET] error in ajax request trying modify product quantity');
        });
    }

    function processAjax($link){
        let content = $link.html();
        $link.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i>');
        console.log('basket::processAjax');
        $.ajax({
            url: $link.attr('href'),
            method: "post",
            data: {}
        }).done(function(data){
            if(data.type == 'success')
            {
                $link.removeClass('disabled').html(content);
            }
            else {
                $link.html('Une erreur est survenue');
                console.log('[BASKET] error in ajax request trying modify product quantity');
            }
        });
    }
});