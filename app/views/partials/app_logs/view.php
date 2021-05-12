
<?php
$comp_model = new SharedController;
$current_page = get_current_url();
$csrf_token = Csrf :: $token;

//Page Data Information from Controller
$data = $this->view_data;

//$rec_id = $data['__tableprimarykey'];
$page_id = Router::$page_id; //Page id from url

$view_title = $this->view_title;

$show_header = $this->show_header;
$show_edit_btn = $this->show_edit_btn;
$show_delete_btn = $this->show_delete_btn;
$show_export_btn = $this->show_export_btn;

?>

<section class="page">
    
    <?php
    if( $show_header == true ){
    ?>
    
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            
            <div class="row ">
                
                <div class="col-12 ">
                    <h3 class="record-title">View  App Logs</h3>
                    
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
                
                <div class="col-md-12 comp-grid">
                    
                    <?php $this :: display_page_errors(); ?>
                    
                    <div  class=" animated fadeIn">
                        <?php
                        
                        $counter = 0;
                        if(!empty($data)){
                        $rec_id = (!empty($data['log_id']) ? urlencode($data['log_id']) : null);
                        
                        
                        
                        $counter++;
                        ?>
                        <div class="page-records ">
                            <table class="table table-hover table-borderless table-striped">
                                <!-- Table Body Start -->
                                <tbody>
                                    
                                    <tr>
                                        <th class="title"> Log Id :</th>
                                        <td class="value"> <?php echo $data['log_id']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Timestamp :</th>
                                        <td class="value"> <?php echo $data['Timestamp']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Action :</th>
                                        <td class="value"> <?php echo $data['Action']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Tablename :</th>
                                        <td class="value"> <?php echo $data['TableName']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Recordid :</th>
                                        <td class="value"> <?php echo $data['RecordID']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Sqlquery :</th>
                                        <td class="value"> <?php echo $data['SqlQuery']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Userid :</th>
                                        <td class="value"> <?php echo $data['UserID']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Serverip :</th>
                                        <td class="value"> <?php echo $data['ServerIP']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Requesturl :</th>
                                        <td class="value"> <?php echo $data['RequestUrl']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Requestdata :</th>
                                        <td class="value"> <?php echo $data['RequestData']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Requestcompleted :</th>
                                        <td class="value"> <?php echo $data['RequestCompleted']; ?> </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <th class="title"> Requestmsg :</th>
                                        <td class="value"> <?php echo $data['RequestMsg']; ?> </td>
                                    </tr>
                                    
                                    
                                </tbody>
                                <!-- Table Body End -->
                                <tfoot>
                                    <tr>
                                        
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="p-3">
                            
                            
                            
                            
                            <button class="btn btn-sm btn-primary export-btn">
                                <i class="fa fa-save"></i> 
                            </button>
                            
                            
                        </div>
                        <?php
                        }
                        else{
                        ?>
                        <!-- Empty Record Message -->
                        <div class="text-muted p-3">
                            <i class="fa fa-ban"></i> No Record Found
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
    
</section>
