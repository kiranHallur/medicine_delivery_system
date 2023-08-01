function remove(ele, id){
    var cnf = confirm('Confirm to cancel order ?');
    if(cnf==true){
        var formData = new FormData();
        formData.append('pk', id);
        formData.append('_token', tokenId);
        extendToastrTimeOut();
        toastr.info('Please wait...'); 

        let result = postFetch(`${api_url}order/cancel`, formData);

        result.then(function (data) {
            clearToastr();
            if (data["success"]) {
                toastr.success(data["msg"]); 
            } else {
                toastr.error(data["msg"]);
            }
        }).catch(function (err) {
            console.log('Error', err);
        });
    }
}