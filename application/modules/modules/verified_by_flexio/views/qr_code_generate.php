<div class="page-header">
	<h1>
		Generate the QR Code
	</h1>
</div><!-- /.page-header -->
<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS --> 
        <?php echo $form_open; ?> 
    
        
        <div class="row">
            <div class="col-md-6 col-md-offset-3"> 
                <input type="radio" name="display_format" id="display_format_image" value="image" checked="checked">Download as Image
                <input type="radio" name="display_format" id="display_format_print" value="print_dialog">Display Print Dialog
            </div> 
        </div>
 
        <div class="space-22"></div>
 
        <div class="row">
            <div class="col-md-6 col-md-offset-3"> 
                <div class="col-md-6"> 
                    Quality of QR Code
                    <select name="level">
                        <option selected="" value="L">L - Smallest</option>
                        <option value="M">M - Medium</option>
                        <option value="Q">Q - Good</option>
                        <option value="H">H - Best</option>
                    </select>
                </div> 
                <div class="col-md-6"> 
                    Size of QR Code
                    <select name="size">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option selected="" value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div> 
            </div>
        </div> 
 
        <div class="space-22"></div>
 
 
        <div class="row center">
            <div class="col-md-6 col-md-offset-3"> 
                <button class="btn btn-info btn-sm btn-round" type="submit">
                    <i class="ace-icon fa fa-check"></i>
                    Generate Queuevite Card
                </button>
 
                &nbsp; &nbsp; &nbsp;
                <button class="btn btn-sm btn-round" type="reset">
                    <i class="ace-icon fa fa-undo"></i>
                    Reset
                </button>
 
                &nbsp; &nbsp; &nbsp; 
                <button class="btn btn-info btn-sm btn-round back" type="button">
                    <i class="ace-icon fa fa-reply"></i>
                    Cancel
                </button> 
                
            </div>
        </div>  
 
        </form>  
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
</div><!-- /.row -->