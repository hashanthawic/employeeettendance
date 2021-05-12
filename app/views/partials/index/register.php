
<?php
	$comp_model = new SharedController;
	$current_page = get_current_url();
	$csrf_token = Csrf :: $token;
	
	$show_header = $this->show_header;
	$view_title = $this->view_title;
	$redirect_to = $this->redirect_to;

?>

	<section class="page">
		
<?php
	if( $show_header == true ){
?>

		<div  class="bg-light p-3 mb-3">
			<div class="container">
				
				<div class="row ">
					
		<div class="col-sm-6 ">
			<h3 class="record-title">User registration</h3>

		</div>

		<div class="col-sm-6 comp-grid">
			<div class="">
	<div class="text-center">
		Already have an account?  <a class="btn btn-primary" href="<?php print_link('') ?>"> Login</a>
	</div>
</div>
		</div>

				</div>
			</div>
		</div>

<?php
	}
?>

		<div  class="">
			<div class="container">
				
				<div class="row ">
					
		<div class="col-md-7 comp-grid">
			
	<?php $this :: display_page_errors(); ?>
	
	<div  class=" animated fadeIn">
		<form id="empattendance-userregister-form" role="form" novalidate enctype="multipart/form-data" class="form form-horizontal needs-validation" action="<?php print_link("index/register?csrf_token=$csrf_token") ?>" method="post">
		<div>
		
								
								<div class="form-group ">
									<div class="row">
										<div class="col-sm-4">
											<label class="control-label" for="Name">Name <span class="text-danger">*</span></label>
										</div>
										<div class="col-sm-8">
											<div class="">
												<input id="ctrl-Name"  value="<?php  echo $this->set_field_value('Name',''); ?>" type="text" placeholder="Enter Name"  required="" name="Name"  class="form-control " />
									 
 
												
											</div>
											 
											
										</div>
									</div>
								</div>
				
				

								
								<div class="form-group ">
									<div class="row">
										<div class="col-sm-4">
											<label class="control-label" for="Username">Username <span class="text-danger">*</span></label>
										</div>
										<div class="col-sm-8">
											<div class="">
												<input id="ctrl-Username"  value="<?php  echo $this->set_field_value('Username',''); ?>" type="text" placeholder="Enter Username"  required="" name="Username"  data-url="api/json/empattendance_Username_value_exist/" data-loading-msg="Checking availability ..." data-available-msg="Available" data-unavailable-msg="Not available" class="form-control  ctrl-check-duplicate" />
									 
<div class="check-status"></div> 
												
											</div>
											 
											
										</div>
									</div>
								</div>
				
				

								
								<div class="form-group ">
									<div class="row">
										<div class="col-sm-4">
											<label class="control-label" for="Password">Password <span class="text-danger">*</span></label>
										</div>
										<div class="col-sm-8">
											<div class="">
												<input id="ctrl-Password"  value="<?php  echo $this->set_field_value('Password',''); ?>" type="password" placeholder="Enter Password"  required="" name="Password"  class="form-control " />
									 
 
												
											</div>
											 
											
										</div>
									</div>
								</div>
				
				
								
								<div class="form-group ">
									<div class="row">
										<div class="col-sm-4">
											<label class="control-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
										</div>
										<div class="col-sm-8">
											<div class="">
												
<input id="-confirm"  class="form-control " type="password" name="confirm_password" required placeholder="Confirm Password" />
 
												
											</div>
											 
											
										</div>
									</div>
								</div>
				
				

								
								<div class="form-group ">
									<div class="row">
										<div class="col-sm-4">
											<label class="control-label" for="Registrationdate">Registrationdate <span class="text-danger">*</span></label>
										</div>
										<div class="col-sm-8">
											<div class="input-group">
												
<input id="ctrl-Registrationdate" class="form-control datepicker  datepicker" required="" value="<?php  echo $this->set_field_value('Registrationdate',''); ?>" type="datetime"  name="Registrationdate" placeholder="Enter Registrationdate" data-enable-time="true" data-min-date="" data-max-date="" data-date-format="Y-m-d H:i:S" data-alt-format="F j, Y - H:i" data-inline="false" data-no-calendar="false" data-mode="single" /> 
												
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
								</div>
							
											</div>
											 
											
										</div>
									</div>
								</div>
				
				

								
								<div class="form-group ">
									<div class="row">
										<div class="col-sm-4">
											<label class="control-label" for="Contactnumber">Contact Number <span class="text-danger">*</span></label>
										</div>
										<div class="col-sm-8">
											<div class="">
												<input id="ctrl-Contactnumber"  value="<?php  echo $this->set_field_value('Contactnumber',''); ?>" type="tel" placeholder="Enter Contact Number"  required="" name="Contactnumber"  data-url="api/json/empattendance_Contactnumber_value_exist/" data-loading-msg="Checking availability ..." data-available-msg="Available" data-unavailable-msg="Not available" class="form-control  ctrl-check-duplicate" />
									 
<div class="check-status"></div> 
												
											</div>
											 
											
										</div>
									</div>
								</div>
				
				


		</div>
		<div class="form-group form-submit-btn-holder text-center">
			<button class="btn btn-primary" type="submit">
				
				<i class="fa fa-send"></i>
			</button>
		</div>
	</form>
	</div>

		</div>

				</div>
			</div>
		</div>

	</section>
