<!-- Notification -->
<?php 
    // dd(Session::get("test_session"));
    $remove_btn = "this.parentElement.remove()";
    $key_name = "notification";
    if(Session::get($key_name)){ 
        $notification = Session::get($key_name);
        if(is_array($notification)){
            foreach($notification as $k=>$v){
                ?>
                <div class="alert alert-info bg-primary text-white alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button> 
                    {{$v["content"]}}
                </div>
                <?php
            }
        }else{
            ?>
            <div class="alert alert-info bg-primary text-white alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button> 
                {{$notification}}
            </div>
            <?php
        }
} ?>



<?php 
    
    $key_name = "success";
    if(Session::get($key_name)){ 
        $notification = Session::get($key_name);
        if(is_array($notification)){
            foreach($notification as $k=>$v){
                ?>
                <div class="alert alert-success bg-success text-white alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button> 
                    {{$v["content"]}}
                </div>
                <?php
            }
        }else{
            ?>
            <div class="alert alert-success bg-success text-white alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button> 
                {{$notification}}
            </div>
            <?php
        }
} ?>

<?php 
    
    $key_name = "error";
    if(Session::get($key_name)){ 
        $notification = Session::get($key_name);
        if(is_array($notification)){
            foreach($notification as $k=>$v){
                ?>
                <div class="alert alert-danger bg-danger text-white alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button> 
                    {{$v["content"]}}
                </div>
                <?php
            }
        }else{
            ?>
            <div class="alert alert-danger bg-danger text-white alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button> 
                {{$notification}}
            </div>
            <?php
        }
} ?>

<?php 
    
    $key_name = "warning";
    if(Session::get($key_name)){ 
        $notification = Session::get($key_name);
        if(is_array($notification)){
            foreach($notification as $k=>$v){
                ?>
                <div class="alert alert-warning bg-warning text-white alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button> 
                    {{$v["content"]}}
                </div>
                <?php
            }
        }else{
            ?>
            <div class="alert alert-warning bg-warning text-white alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" onclick={{$remove_btn}} aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button> 
                {{$notification}}
            </div>
            <?php
        }
} ?>
