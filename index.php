<?php session_start(); ?>
<?php include 'header.php'; ?>
<body class="hold-transition login-page" style="background-image: url('images/bg2.png'); background-size: cover; height: 80vh; width: 100vw; margin: 0;">
   <div class="login-box" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div class="login-logo" style="margin-top: -70px; width: 500px; display: flex; flex-direction: column; align-items: center;">
            <img src="images/UC1.png" alt="Logo" style="max-width: 100%; max-height: 100%;">
            <p id="date" style="color: #ededed; font-family: 'Times New Roman', sans-serif;"></p>
            <p id="time" class="bold" style="color: #ededed; font-family: 'Times New Roman', sans-serif;"></p>
        </div>
        <div class="login-box-body">
            <h4 class="login-box-msg">Enter Employee ID</h4>
            <form id="attendance">
                <div class="form-group">
                    <select class="form-control" name="status">
                        <option value="in">Time In</option>
                        <option value="out">Time Out</option>
                    </select>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" class="form-control input-lg" id="employee" name="employee" required>
                </div>
                <div class="row">
                    <div class="col-xs-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" name="signin"><i class="fa fa-sign-in"></i> Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</div>
		<div class="alert alert-success alert-dismissible mt20 text-center" style="display:none;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span>
    </div>
		<div class="alert alert-danger alert-dismissible mt20 text-center" style="display:none;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span>
    </div>
  		
</div>
	
<?php include 'scripts.php' ?>
<script type="text/javascript">
$(function() {
  var interval = setInterval(function() {
    var momentNow = moment();
    $('#date').html(momentNow.format('dddd').substring(0,3).toUpperCase() + ' - ' + momentNow.format('MMMM DD, YYYY'));  
    $('#time').html(momentNow.format('hh:mm:ss A'));
  }, 100);

  $('#attendance').submit(function(e){
    e.preventDefault();
    var attendance = $(this).serialize();
    $.ajax({
      type: 'POST',
      url: 'attendance.php',
      data: attendance,
      dataType: 'json',
      success: function(response){
        if(response.error){
          $('.alert').hide();
          $('.alert-danger').show();
          $('.message').html(response.message);
        }
        else{
          $('.alert').hide();
          $('.alert-success').show();
          $('.message').html(response.message);
          $('#employee').val('');
        }
      }
    });
  });
    
});
</script>
</body>
</html>