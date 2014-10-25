<section class="offCanvasPanel" id="submitTrack">
			<h4>Submit a track to The Bearded Buffalo</h4>
			<hr>
  			<div id="form--wrapper">
    		  <!--<form>
    			<div class="fieldgroup song">
      				<input type="text" class="pull-left" placeholder="Song title">
      				<span class="matcher">by</span>
      				<input type="text" class="pull-right" placeholder="Artist">
      			</div>
      		    <div class="fieldgroup words">
      		      <textarea placeholder="Five Words about the track"></textarea>
        		  <button class="pull-right">Submit →</button>
        		  <a class="button pull-right" id="close-form" style="margin-right:5px;">Close ×</a>
      			</div>
     		 </form>-->
     		 <?php gravity_form("Submit Song", $display_title=false, $display_description=false, $display_inactive=false, $field_values=null, $ajax=false, $tabindex); ?>
    		</div>
</section>