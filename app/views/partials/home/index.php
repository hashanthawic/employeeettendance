
<?php 
$page_id = null;
$comp_model = new SharedController;
$current_page = get_current_url();
?>
<div>
    
    <div  class="">
        <div class="container">
            
            <div class="row ">
                
                <div class="col-md-12 comp-grid">
                    
                </div>
                
            </div>
        </div>
    </div>
    
    <div  class="">
        <div class="container">
            
            <div class="row ">
                
                <div class="col-md-12 comp-grid">
                    
                </div>
                
                <div class="col-md-4 comp-grid">
                    
                    <?php $rec_count = $comp_model->getcount_empattendance();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("empattendance/") ?>">
                        <div class="row">
                            <div class="col-2">
                                <i class="fa fa-globe"></i>
                            </div>
                            <div class="col-10">
                                <div class="flex-column justify-content align-center">
                                    <div class="title">Emp Attendance</div>
                                    
                                    <small class=""></small>
                                </div>
                            </div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                        
                    </a>
                    
                </div>
                
                <div class="col-md-4 comp-grid">
                    
                    <?php $rec_count = $comp_model->getcount_checkin();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("checkin/") ?>">
                        <div class="row">
                            <div class="col-2">
                                <i class="fa fa-globe"></i>
                            </div>
                            <div class="col-10">
                                <div class="flex-column justify-content align-center">
                                    <div class="title">Checkin</div>
                                    
                                    <small class=""></small>
                                </div>
                            </div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                        
                    </a>
                    
                </div>
                
                <div class="col-md-4 comp-grid">
                    
                    <?php $rec_count = $comp_model->getcount_checkout();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("checkout/") ?>">
                        <div class="row">
                            <div class="col-2">
                                <i class="fa fa-globe"></i>
                            </div>
                            <div class="col-10">
                                <div class="flex-column justify-content align-center">
                                    <div class="title">Checkout</div>
                                    
                                    <small class=""></small>
                                </div>
                            </div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                        
                    </a>
                    
                </div>
                
            </div>
        </div>
    </div>
    
</div>
