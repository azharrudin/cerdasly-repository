function ui_ranklist(){
    Swal.fire({
        text: "Memuat....."
    })
    $.ajax({
        url: "/",
        method: "GET",
        success: function(data){
            Swal.close()
            Swal.fire({
                html: $(data).find("#ui_ranklist").html()
            });
        },
        complete: function(){
        }
    })
    
}