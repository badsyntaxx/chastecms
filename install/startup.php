<?php 

    require_once ROOT_DIR . '/install/install.php';
    $install = new Install();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Install Chaste CMS</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Titillium+Web:300,300i,400,400i,600,600i,700,700i');
        *, *:before, *:after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            outline: none
        }
        body {
            width:60%;
            padding-top:50px;
            margin:auto;
            font-family:'Titillium Web';
            font-size:18px;
            line-height:24px;
            background-color:#2a4767;
            color:#fff;
        }
        form {
            width:1024px;
            margin:auto;
        }
        span {
            font-size:24px;
            line-height: 24px;
        }
        p {
            margin:0;
        }
        label {
            float:left;
            width:100%;
            margin-bottom: 5px;
        }
        input {
            width:100%;
            border:none;
            border-radius: 7px;
            margin-bottom: 10px;
            line-height: 35px;
            padding:0 15px;
            box-shadow: inset 0px 0px 5px 0px rgba(0,0,0,0.75);
        }
        hr {
            margin: 3px 0 5px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3)
        }
        .row {
            float:left;
            width:100%;
        }
        .column {
            float:left;
            width:49%;
            margin-right:2%;
        }
        .column:nth-child(2n+2) {
            margin-right:0;
        }
        #submit {
            float:left;
            min-width:100px;
            border:1px solid #aaa;
            border-radius:5px;
            line-height:35px;
            padding:0 15px;
            margin-top:20px;
        }
        .alert-area {
            margin-bottom:10px;
        }
        .alert {
            position: relative;
            float: left;
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            font-size: 18px;
            line-height: 18px;
            text-align: left
        }
        .alert strong {
            text-transform: capitalize;
        }
        .alert.success {
            background-color: #76d769;
        }
        .alert.error {
            background-color: #ff6868;
        }
    </style>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <form id="install">
        <h1 class="row" style="margin-bottom:100px;">Install Chaste</h1>
        <div class="row alert-area"></div>
        <div class="row">       
            <div class="column">
                <span>Credentials</span>
                <hr>
                <label>Username</label>
                <input type="text" name="username" class="required">
                <label>Email</label>
                <input type="email" name="email" class="required">
                <label>Password</label>
                <input type="password" name="password" class="required">
                <label>Confrim Password</label>
                <input type="password" name="confirm" class="required">
            </div>
            <div class="column">
                <span>Database</span>
                <hr>
                <label>Host</label>
                <input type="text" name="host" class="required">
                <label>Database</label>
                <input type="text" name="db" class="required">
                <label>Username</label>
                <input type="text" name="db_username" class="required">
                <label>Password</label>
                <input type="text" name="db_password" class="required">
            </div>
        </div>
        <div class="row">
            <button type="button" id="submit" disabled><i class="fas fa-share-square fa-fw"></i> Install</button>
        </div>
    </form>
    <div class="response-wrapper"></div>
</body>
<script type="text/javascript">

$('body').on('keyup change', '#install', function() {

    var complete = true;
    var error = false;
    var inputs = $('#install').find(':input'); // Should return all input elements in that specific form.

    $('.required').each(function() {
        if ($(this).val() == '') {
            complete = false;
        }
    });

    if (complete == true) {
        $('#submit').prop('disabled', false);
    } 
    if (complete == false) {
        $('#submit').prop('disabled', true);
    }
});

$('body').on('click', '#submit', function() {
    var btn = $(this);
    var btnhtml = btn.html();
    $(this).html('<i class="fas fa-spinner fa-fw fa-spin"></i> loading...');
    $.ajax({
        url: '/install/install',
        type: 'POST',
        data: $('#install').serialize(),
        success: function(response, status, xhr) {
            if ($.trim(response)) {
                var data = JSON.parse(response);
                $('.alert-area').html('<div class="alert ' + data.alert + '"><strong>' + data.alert + '!</strong> ' + data.message);
            } 
        },
        complete: function() {
            btn.prop('disabled', false).html(btnhtml);
        }
    });
});

</script>
</html>