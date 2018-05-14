function send_request(request, item){
    $.ajax(
    {   url:"basketAction.php",
        method: "POST",
        data:{"action" : request, "id" : item}
    }
    ).done((data) => {
        var it = '#'+item
        var data = JSON.parse(data)
        switch(request)
        {
            case "add":
                break
            case "remove":
                if(data.state == "OK"){
                    $(it.replace(' ', '_')).remove();
                    showModal("Usunięto element")
                }
                else
                    showModal(data.msg)
                break
            case "clear":
                if(data.state == "OK"){
                    clearTable(it)
                    showModal("Wyczyszczono")
                }
                else
                    showModal(data.msg)
                break
            case "buy":
                if(data.state == "OK")
                {
                    showModal("Dokonano zakupu")
                    clearTable(it)
                }
                else
                    showModal(data.msg)
                break
        }
    }).fail( (_) =>
    {
        showModal("Błędna odpowiedź serwera")
    }
    )
}

function clearTable(tableItem){
    $(tableItem+ " table tbody").remove()
}

function showModal(msg){
    $("#dialog-message").empty().append( msg ) ;

    $( "#dialog-modal" ).dialog({
        open : function(eve, ui) {
            window.setTimeout(function(item) {
                $('#dialog-modal').dialog('close');
                }, 
            2000);
            },
        modal: true,
        buttons: {
        Ok: function() {
            $( this ).dialog( "close" );
        }
        }
    });
       
}