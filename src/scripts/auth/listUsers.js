$(function(event) {
    $(".delete").click(function(e){
        e.stopPropagation();
        e.preventDefault();
        window.deleteHref = $(e.target).attr('href');
        $('.deletemodal').modal('show');
    })

    $(".deletemodal").modal('setting', {
        onApprove: function(){
            window.location = window.deleteHref;
        }
    });
});