<?php
$arr = [
    "dealer" => [
        ["title" => "Products", "href"=>"", "drop_down" => [
                ["title" => "List Products", "href"=> route('products.list')],
                ["title" => "Create Products", "href"=> route('product.create')],
            ]
        ],
        ["title" => "Stock", "href"=>"#","drop_down" => [
            ["title" => "List Stocks", "href"=> route('stocks')],
            ["title" => "Create Stock", "href"=> route('stock.create')],
        ]
        ],
        ["title" => "Supply", "href"=>route('supply')],
        ["title" => "Profile", "href"=>route('user.profile.edit')],
        ["title" => "Logout", "href"=>route('logout')],
    ],
    "retailer" => [
        ["title" => "Products", "href"=>"", "drop_down" => [
                ["title" => "List Products", "href"=> route('products.list')],
                ["title" => "Create Products", "href"=> route('product.create')],
            ]
        ],
        ["title" => "Stock", "href"=>"#","drop_down" => [
                ["title" => "List Stocks", "href"=> route('stocks')],
                ["title" => "Create Stock", "href"=> route('stock.create')],
            ]
        ],
        ["title" => "Orders", "href"=>"#","drop_down" => [
                ["title" => "List Orders", "href"=> route('orders')],
                ["title" => "Create Order", "href"=> route('order.create')],
            ]
        ],
        ["title" => "Item Returns", "href"=> route('order.item.return.list') ],
        ["title" => "Order Cancel or Returns", "href"=> route('order.cancel_or_return.list') ],
        ["title" => "Supply", "href"=>route('supply')],
        ["title" => "Profile", "href"=>route('user.profile.edit')],
        ["title" => "Logout", "href"=>route('logout')],
    ],
    "customer" => [
        ["title" => "Order Medicines", "href"=>"#","drop_down" => [
                ["title" => "List Orders", "href"=> route('orders')],
                ["title" => "Order", "href"=> route('order.create')],
            ]
        ],
        ["title" => "Profile", "href"=>route('user.profile.edit')],
        ["title" => "Logout", "href"=>route('logout')],
    ],
    "admin" => [
        ["title" => "Products", "href"=> route('products.list')],
        ["title" => "Stock", "href" => route('stocks')],
        ["title" => "Supply", "href" => route('supply')],
        ["title" => "Profile", "href"=> route('user.profile.edit')],
        ["title" => "Users", "href" => route('users')],
        ["title" => "Logout", "href"=>route('logout')],
    ]
];

if($global_session['role_id'] == Config('constants.DEALER_ROLE_ID')){
    $arr = $arr['dealer'];
}else if($global_session['role_id'] == Config('constants.RETAILER_ROLE_ID')){
    $arr = $arr['retailer'];
}else if($global_session['role_id'] == Config('constants.CUSTOMER_ROLE_ID')){
    $arr = $arr['customer'];
}else if($global_session['role_id'] == Config('constants.ADMIN_ROLE_ID')){
    $arr = $arr['admin'];
}

// dd($arr, $global_session);

?>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <!-- Brand -->
<a class="navbar-brand" href="#">{{config('constants.company_name')}} | {{text_cap($global_session->role->name)}}</a>

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Navbar links -->
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
        @if($arr)
            @foreach($arr as $k => $v)
            <li class="nav-item {{($v['drop_down'] ?? NULL) ? 'dropdown' : ''}}  ">
                <a class="nav-link {{($v['drop_down'] ?? NULL) ? 'dropdown-toggle' : ''}}" {{($v['drop_down'] ?? NULL) ? "id=collapsibleNavbar$k data-toggle=dropdown" : ''}}  href="{{$v['href']}}">{{$v['title']}}</a>
                @isset($v['drop_down'])
                    <div class="dropdown-menu">
                        @foreach($v['drop_down'] as $kk => $vv)
                            <a class="dropdown-item" href="{{$vv['href']}}">{{$vv['title']}}</a>
                        @endforeach
                    </div>
                @endisset
            </li>
            @endforeach
        @endif
    </ul>
  </div>
</nav>