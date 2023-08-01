let validate_item_form = {
    "error_class" : "required",
    "elements" : {
        "#product_id" : 'required',
        "#stock_qty" : ['required', 'integer'],
        "#initial_qty" : ['required', 'integer'],
    
    },
    "error_messages" : {
        "#product_id.required" : 'Choose any one product',
    }
};
let ordered_to_user_id_element = document.getElementById('ordered_to_user_id');
let index_element = document.getElementById('index_field'); 
let stock_item_element = document.getElementById('stock_item_id'); 
let product_element = document.getElementById('product_id');
let barcode_element = document.getElementById('barcode');
let initial_qty_element = document.getElementById('initial_qty');
let stock_qty_element = document.getElementById('stock_qty');
let is_closed_element = document.getElementById('is_closed');
let items_table_element = document.getElementById('items-table');
let items_table_tbody_element = items_table_element.querySelector('tbody');

let user_products_table = document.getElementById('user-products-table');
let user_products_table_tbody = user_products_table.querySelector('tbody');

let order_totals = {
    "qty" : 0,
    "total_amount" : 0,
};

function show_user_stock_products(element){
    let value = element.value;
    if(value!=""){
        let res = getFetch(`${api_url}order/load-user-products/${value}`);
        res.then((data) => {
            user_stock_products_tbl(data);
        });
    }else{
        user_products_table_tbody.innerHTML = "";
    }
}

function user_stock_products_tbl(stocks=[]){
    let content = ``;
    stocks.forEach((item, index) => {
        content += `
            <tr>
                <td id="name_${index}" >${item['name']}</td>
                <td id="price_per_${index}" >${item['price_per']}</td>
                <td>${item['stock_qty']}</td>
                <td>
                    <input type="number" id="purchase_qty_${index}" value="" >
                    <input type="hidden" id="product_id_${index}" value="${item['product_id']}" class=""  >
                    <input type="hidden" id="stock_id_${index}" value="${item['stock_id']}" class=""  >
                    <input type="hidden" id="stock_item_id_${index}" value="${item['stock_item_id']}">
                    <input type="hidden" id="stock_qty_${index}" value="${item['stock_qty']}">
                </td>
                <td>
                    <button type="button" class="btn btn-primary btn-md" onclick="push_item(${index})">Add to Cart</button>
                </td>
            </tr>        
        `;
    });

    if(content==""){
        content = `
            <tr>
                <td colspan="5" >No products available</td>
            </tr>        
        `;
    }

    user_products_table_tbody.innerHTML = content;
}


function push_item(index) {    
    let obj = get_user_products_table_row_data(index);
    console.log(obj);
    if(obj['purchase_qty']<=0){
        return alert("Enter purchase quantity.");
    }
    if(obj['purchase_qty']>obj['stock_qty']){
        return alert("Purchase quantity must be less than stock quantity.");
    }
    items_container['items'].push(new Item(obj));
    toastr.success("Item added to cart");
    table_render();
} 

function remove_item(index, order_item_id=null){
    
    // let cfn = confirm("Confirm to remove");
    // if(!cfn){
    //     return false;
    // }

    if(order_item_id==null){
        items_container['items'].splice(index,1);
        table_render();
    }else{
        toastr.info("Removing item...");
        let form_data = new FormData();
        form_data.append('_token', tokenId);
        form_data.append('pk', order_item_id);
        let res = postFetch(`${api_url}order/item/remove`, form_data);
        res.then((data) => {
            toastr.clear();
            if(data['success']){
                items_container['items'].splice(index,1);
                toastr.success(data['msg']);
                table_render();

            }else{
                toastr.error(data['msg']);
            }
        });
    }   
}



function edit_item(index, stock_item_id=null){
    let obj = items_container['items'][index];

    index_element.value = index;
    stock_item_element.value = stock_item_id;
    product_element.value = obj['product_id'];
    barcode_element.value = (obj['barcode']!=null && obj['barcode']!="null")? obj['barcode'] : '';
    initial_qty_element.value = obj['initial_qty'];
    stock_qty_element.value = obj['stock_qty'];
    is_closed_element.checked = (obj['is_closed']==0)? false : true;
}

function empty_item_form_data(){
        index_element.value="",
        stock_item_element.value="",
        product_element.value=0
        barcode_element.value = "";
        initial_qty_element.value = "";
        stock_qty_element.value = "",
        is_closed_element.checked=false
}

function get_user_products_table_row_data(index){
    return {
        "product_id" : document.getElementById(`product_id_${index}`).value || null,
        "stock_id" : document.getElementById(`stock_id_${index}`).value || null,
        "stock_item_id" : document.getElementById(`stock_item_id_${index}`).value || null,
        "name" : document.getElementById(`name_${index}`).innerText || "Not available",
        "purchase_qty" : document.getElementById(`purchase_qty_${index}`).value || 0,
        "stock_qty" : parseInt(document.getElementById(`stock_qty_${index}`).value) || 0,
        "price_per" : parseFloat(document.getElementById(`price_per_${index}`).innerText) || 0,
        "total_price" : 0,
    }
}

let items_container = {
    "items" : [], 
}

class Item{
    constructor(data){
        Object.assign(this,data)
        this.total_price = this.price_per*this.purchase_qty;
    }
}

function table_render(){
    let context = ``;
    order_totals["qty"] = 0;
    order_totals["total_amount"] = 0;
    items_container['items'].forEach((item, index)=>{
        order_totals["qty"] += item['purchase_qty'];
        order_totals["total_amount"] += item['total_price'];
        context += `
            <tr>
                <td>
                    <p>${item['name']}</p>
                    ${
                        
                        (item['purchase_qty'] > item['stock_qty'])? 
                            `<label class="required" >No stock available...</label>`
                        : ''
                    }
                </td>
                <td>${item['purchase_qty']}</td>
                <td>${item['price_per']}</td>
                <td>${item['total_price']}</td> 
                <td>

                ${
                    (current_user_role_id != app_constants["ADMIN_ROLE_ID"])?
                        `<button type="button" onclick="remove_item(${index}, ${item['order_item_id'] || null})" class="btn btn-danger btn-sm">Remove</button>`
                    : ''
                }
                    
        ${
            (item['order_item_id'] == undefined || item['order_item_id'] == null)?
                `<input type="hidden" name="items[${index}][product_id]" value="${item['product_id']}">
                 <input type="hidden" name="items[${index}][stock_id]" value="${item['stock_id']}">
                 <input type="hidden" name="items[${index}][stock_item_id]" value="${item['stock_item_id']}">
                 <input type="hidden" name="items[${index}][purchase_qty]" value="${item['purchase_qty']}">
                 <input type="hidden" name="items[${index}][price_per]" value="${item['price_per']}">
                 <input type="hidden" name="items[${index}][total_price]" value="${item['total_price']}">`
            : ''

        }

        ${
            (current_user_role_id != app_constants["CUSTOMER_ROLE_ID"] && current_user_role_id != app_constants["ADMIN_ROLE_ID"] && item['is_order_items_deducted'] == false)?
                `<button type="button" onclick="deduct_item(${index}, ${item['order_item_id'] || null})" class="btn btn-success btn-sm">Deduct Item</button>`
            : ''
        }

        ${
            (current_user_role_id != app_constants["CUSTOMER_ROLE_ID"] && current_user_role_id != app_constants["ADMIN_ROLE_ID"] && item['is_order_items_deducted'] == true)?
                `<button type="button" onclick="rollback_item(${index}, ${item['order_item_id'] || null})" class="btn btn-warning btn-sm">Rollback Item</button>`
            : ''
        }

                </td>
            </tr>
        `;
    });

    items_table_tbody_element.innerHTML = context;
    document.getElementById('total_qty').innerText = order_totals["qty"];
    document.getElementById('total_amount').innerText = order_totals["total_amount"];
}

function deduct_item(index, order_item_id){
    let cfn = confirm("Confirm to deduct item from stock.");
    if(!cfn){
        return false;   
    }

    let formData = new FormData();
    formData.append('order_item_id', order_item_id);
    formData.append('_token', tokenId);
    let res = postFetch(`${api_url}supply/item/deduct`,formData);
    let current_event = event;
    res.then(json => {
        json["event"] = current_event;
        json["index"] = index;
        json["order_item_id"] = order_item_id;
        return json;
    }).then((data) => {
        
        let button = data["event"].target;

        if(data["success"]){
            toastr.success(data["msg"]);
            
            button.setAttribute("onclick", `rollback_item(${data["index"]}, ${data["order_item_id"]})`)
            button.setAttribute("class", `btn btn-warning btn-sm`);
            button.innerText = "Rollback Item";
            
        }else{
            toastr.error(data["msg"]);
        }
    });
}

function rollback_item(index, order_item_id){
    let cfn = confirm("Confirm to rollback deducted item from stock.");
    if(!cfn){
        return false;   
    }

    let formData = new FormData();
    formData.append('order_item_id', order_item_id);
    formData.append('_token', tokenId);
    let res = postFetch(`${api_url}supply/item/rollback`,formData);
    let current_event = event;
    res.then(json => {
        json["event"] = current_event;
        json["index"] = index;
        json["order_item_id"] = order_item_id;
        return json;
    }).then((data) => {

        let button = data["event"].target;

        if(data["success"]){
            toastr.success(data["msg"]);
            
            button.setAttribute("onclick", `deduct_item(${data["index"]}, ${data["order_item_id"]})`)
            button.setAttribute("class", `btn btn-success btn-sm`);
            button.innerText = "Deduct Item";
            
        }else{
            toastr.error(data["msg"]);
        }
    });
}