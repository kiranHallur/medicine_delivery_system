function removeImage(ele, id){
    var cnf = confirm('Confirm to remove ?');
    if(cnf==true){
        var formData = new FormData();
        formData.append('manufacturer', id);
        formData.append('_token', tokenId);
        extendToastrTimeOut();
        toastr.info('Removing...');

        let result = postFetch(`${api_url}backend/manufacturer/image/remove/${id}`, formData);

        result.then(function (data) {
            clearToastr();
            if (data["success"]) {
                toastr.success(data["msg"]);
                ele.parentElement.parentElement.remove();
            } else {
                toastr.error(data["msg"]);
            }
        }).catch(function (err) {
            console.log('Error', err);
        });
    }
}