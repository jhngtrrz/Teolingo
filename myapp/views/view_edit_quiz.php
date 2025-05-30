<script>
  var configuration = <?= $dbinfo_json ?>;
  var l10n = <?= $l10n_json ?>;
  var l10n_js = <?= $l10n_js_json ?>;
  var typeinfo = <?= $typeinfo_json ?>;
  var decoded_3et = <?= $decoded_3et_json ?>;
  var initial_universe = <?= $universe ?>;
  var total_time_seconds = <?= $time_seconds ?? 0 ?>;
  var buffer = 3;
  total_time_seconds = total_time_seconds - buffer;
  if(total_time_seconds < 0)
    total_time_seconds = 0;
  var is_unlimited = <?php echo json_encode($is_unlimited); ?>;

  var submit_to = '<?= site_url("text/submit_quiz") ?>';
  var check_url = '<?= site_url("text/check_submit_quiz") ?>';
  var test_quiz_url = '<?= site_url("text/test_quiz") ?>';
  var import_shebanq_url = '<?= site_url("shebanq/import_shebanq") ?>';
  var quiz_name = '<?= is_null($quiz) ? '' : $quiz ?>';
  var dir_name = '<?= $dir ?>';
  var order_features = <?= $order_features ?>;
  var is_new = '<?= $is_new ?>';
  

</script>

<div class="quizeditor" style="display:none;">
<div id="quiz_tabs">
  <ul>
    <li><a href="#tab_description"><?= $this->lang->line('description') ?></a></li>
    <li><a href="#tab_universe"><?= $this->lang->line('passages') ?></a></li>
    <li><a href="#tab_sentences"><?= $this->lang->line('sentences') ?></a></li>
    <li><a href="#tab_sentence_units"><?= $this->lang->line('sentence_units') ?></a></li>
    <li><a href="#tab_features"><?= $this->lang->line('features') ?></a></li>
    <li><a href="#tab_timer"><?= $this->lang->line('timer') ?></a></li>
  </ul>
   
  <div id="tab_description">
      &nbsp;<!-- &nbsp; is required to circumvent a bug in some versions of Firefox -->
      <textarea id="txtdesc" style="width:100%; height:100px" wrap="hard"></textarea>
  </div>
  <div id="tab_universe">
    <div id="passagetree">
    </div>
    <div style="margin-top:10px"><input id="maylocate_cb" type="checkbox" name="maylocate" value="maylocate"> <?= $this->lang->line('may_locate') ?></div>
    <div style="margin-top:10px">
      <span><?= $this->lang->line('context_sentences') ?> <?= $this->lang->line('sent_before') ?>
        <select id="sentbefore">
          <option value="0">0</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
        </select>
      </span>
      <span><?= $this->lang->line('sent_after') ?>
        <select id="sentafter">
          <option value="0">0</option>
          <option value="1">1</option>
        </select>
      </span>
    </div>
    <div style="margin-top:10px">
      <span><?= $this->lang->line('fixed_questions') ?> <input id="fixedquestions" type="text" size="8"> <span id="fqerror"></span>
    </div>
    <div style="margin-top:10px">
        <span><?= $this->lang->line('question_order') ?> 
            <input type="radio" id="randomorder" name="randomize" value="true">
            <label for="randomorder"><?= $this->lang->line('question_order_random') ?></label>
            <input type="radio" id="fixedorder" name="randomize" value="false">
            <label for="fixedorder"><?= $this->lang->line('question_order_fixed') ?></label>
        </span>
    </div>
  </div>
  <div id="tab_sentences">
  </div>
  <div id="tab_sentence_units">
  </div>
  <div id="tab_features">
  </div>
  <div id="tab_timer">
    <div class="row">
      <label style="margin-right:10px" for="minutes">Minutes: </label> 
      <select class="timer-settings" id="minutes-timer" name="minutes" style="margin-right:20px">
        <?php 
          for ($i = 0; $i < 60; $i++) {
            echo "<option value=\"$i\">$i</option>";
          }
        ?>
      </select>
      <label style="margin-right:10px" for="seconds">Seconds: </label> 
      <select class="timer-settings" id="seconds-timer" name="seconds">
        <?php 
          for ($i = 0; $i < 60; $i++) {
            echo "<option value=\"$i\">$i</option>";
          }
        ?>
      </select>
      <label style="margin-left:20px" for="activate-timer">Timer: </label>
      <select class="timer-settings" style="margin-left:10px" id="activate-timer-menu" name="activate-timer">
        <option value="off">OFF</option>
        <option value="on">ON</option>
      </select>
    </div>

    <div class="row">
      <div style="border:3px; border-color:black" class="clock-container">
        <div class="bordered" id="time-left">00:00</div>
        <div id="display-controls">
          <button id="reset-timer" class="timer-controls btn"><i class="fa" aria-hidden="true">&#xf021;</i></button>
          <button id="increase-arrow-min" class="timer-controls btn"><i class="fa fa-arrow-up"></i></button>
          <button id="decrease-arrow-min" class="timer-controls btn"><i class="fa fa-arrow-down"></i></button>
          <button id="increase-arrow-seconds" class="timer-controls btn"><i class="fa fa-arrow-up"></i></button>
          <button id="decrease-arrow-seconds" class="timer-controls btn"><i class="fa fa-arrow-down"></i></button>
        </div>
      </div>
    </div>

    <br>
  </div>
</div>

<script>
  $(document).ready(function() {
    $("#reset-timer").click(function(){
      console.log('RESET');
      $("#seconds-timer").val("0");
      $("#minutes-timer").val("0");
      $("#time-left").text("00:00");
      $("#activate-timer-menu").val("off");
    })
    $("#decrease-arrow-min").click(function(){
      let minutes = $("#minutes-timer").val();
      let new_minutes = parseInt(minutes) - 1;
      if(new_minutes < 0){
        myalert("Timer Limit Reached", "The timer cannot be negative.");
      }
      else{
        $("#minutes-timer").val(new_minutes);
        $("#minutes-timer").trigger("change");
      }
    })
    $("#increase-arrow-min").click(function(){
      let minutes = $("#minutes-timer").val();
      let new_minutes = parseInt(minutes) + 1;
      if(new_minutes >= 60){
        myalert("Timer Limit Reached", "The timer cannot exceed 60 minutes.");
      }
      else{
        $("#minutes-timer").val(new_minutes);
        $("#minutes-timer").trigger("change");
      }
    })
    $("#decrease-arrow-seconds").click(function(){
      let seconds = $("#seconds-timer").val();
      let minutes = $("#minutes-timer").val();

      let new_seconds = parseInt(seconds) - 15;
      if(new_seconds < 0){
        let new_minutes = parseInt(minutes) - 1;
        if(new_minutes < 0){
          myalert("Timer Limit Reached", "The timer cannot be negative.");
        }
        else{
          console.log('New Seconds: ', new_seconds);
          let residual = 60 + new_seconds;
          $("#seconds-timer").val(residual.toString());
          $("#minutes-timer").val(parseInt(minutes) - 1);
          $("#seconds-timer").trigger("change");
          $("#minutes-timer").trigger("change");
        }
      }
      else{
        $("#seconds-timer").val(new_seconds);
        $("#seconds-timer").trigger("change");
      }
    })
    $("#increase-arrow-seconds").click(function(){
      let seconds = $("#seconds-timer").val();
      let minutes = $("#minutes-timer").val();

      let new_seconds = parseInt(seconds) + 15;
      if(new_seconds >= 60){

        let new_minutes = parseInt(minutes) + 1;
        if(new_minutes >= 60){
          myalert("Timer Limit Reached", "The timer cannot exceed 60 minutes.");
        }
        else{
          console.log('New Seconds: ', new_seconds);
          let residual = new_seconds - 60;
          $("#seconds-timer").val(residual.toString());
          $("#minutes-timer").val(parseInt(minutes) + 1);
          $("#seconds-timer").trigger("change");
          $("#minutes-timer").trigger("change");
        }
      }
      else{
        $("#seconds-timer").val(new_seconds);
        $("#seconds-timer").trigger("change");
      }
    })






    $("#activate-timer-menu").change(function(){
      let timer_status = $("#activate-timer-menu").val();
      if(timer_status =="off"){
        $("#seconds-timer").val("0");
        $("#minutes-timer").val("0");
        $("#time-left").text("00:00");
      }
    });
    $("#seconds-timer").change(function(){
      let timer_status = $("#activate-timer-menu").val();
      if(timer_status =="off"){
        myalert("Timer is Off", "The timer is currently turned off, to set a time limit turn the timer on.");
        $("#seconds-timer").val("0");
      }
    });
    $("#seconds-timer").change(function(){
      let minutes = $("#minutes-timer").val()
      if(minutes.length < 2)
        minutes = "0" + minutes;
      let seconds = $("#seconds-timer").val()
      if(seconds.length < 2)
        seconds = "0" + seconds;
      $("#time-left").text( minutes + ":" + seconds);
    });
    $("#minutes-timer").change(function(){
      let timer_status = $("#activate-timer-menu").val();
      if(timer_status =="off"){
        myalert("Timer is Off", "The timer is currently turned off, to set a time limit turn the timer on.");
        $("#minutes-timer").val("0");
      }
    });
    $("#minutes-timer").change(function(){
      let minutes = $("#minutes-timer").val()
      if(minutes.length < 2)
        minutes = "0" + minutes;
      let seconds = $("#seconds-timer").val()
      if(seconds.length < 2)
        seconds = "0" + seconds;
      $("#time-left").text( minutes + ":" + seconds);
    });
  });
</script>

<div style="display:none" id="virtualkbcontainer">
  <div id="virtualkbid"></div>
  <input id="firstinput" type="text"> <!--Initial attachment point for virtual keyboard --> 
</div>

<div class="buttons">
  <a class="btn btn-primary" href="#" onclick="save_quiz(); return false;"><?= $this->lang->line('save_button') ?></a>
  <a class="btn btn-primary" href="#" onclick="test_quiz(quiz_name, is_new); return false;">Test Exercise</a>
  <a class="btn btn-outline-dark" href="<?=site_url(build_get('file_manager',array('dir' => $dir))) ?>"><?= $this->lang->line('cancel_button') ?></a>
</div>
</div>




<script>
  function updateValue()
  {
    let x = document.getElementById('myInput').value; 
    document.getElementById('myInput').value = x;
  }

</script>


<!-- Dialogs for this page follow -->

 <?php //*********************************************************************
       // Limit features dialog 
       //*********************************************************************
    ?>
<div id="feature-limit-dialog" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('show_only_options') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span id="feature-limit-body"></span>
      </div>
      <div class="modal-footer">
          <button type="button" id="feature-limit-dialog-save" class="btn btn-primary"><?= $this->lang->line('save_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
      </div>
    </div>
  </div>
</div>

 <?php //*********************************************************************
        // Quiz Filename dialog 
        //*********************************************************************
    ?>
  <div id="filename-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('specify_file_name') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="filename-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
            <span id="filename-error-text"></span>
          </div>
          <div class="form-group">
            <label for="filename-name"><?= $this->lang->line('enter_filename_no_3et') ?></label>
            <input type="text" name="filename" id="filename-name" value="<?= $quiz ?>" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="filename-dialog-save" class="btn btn-primary"><?= $this->lang->line('save_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>


 <?php //*********************************************************************
        // Confirm File Overwrite dialog 
        //*********************************************************************
    ?>
<div id="overwrite-dialog-confirm" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('overwrite') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span><?= $this->lang->line('file_exists_overwrite') ?></span>
      </div>
      <div class="modal-footer">
        <button type="button" id="overwrite-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
      </div>
    </div>
  </div>
</div>

 <?php //*********************************************************************
        // Import from SHEBANQ dialog
        //*********************************************************************
    ?>
  <div id="import-shebanq-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('import_from_shebanq') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="import-shebanq-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
            <span id="import-shebanq-error-text"></span>
          </div>
          <div class="form-group">
            <label for="import-shebanq-qid"><?= $this->lang->line('shebanq_query_id_prompt') ?></label>
            <input type="text" name="query-id" id="import-shebanq-qid" value="" class="form-control">
          </div>
          <div class="form-group">
            <label for="import-shebanq-dbvers"><?= $this->lang->line('shebanq_query_id_prompt') ?></label>
            <input type="text" name="db-version" id="import-shebanq-dbvers" value="4b" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="import-shebanq-button" class="btn btn-primary"><?= $this->lang->line('import_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>


 <?php //*********************************************************************
        // Confirm Sentence Unit MQL dialog
        //*********************************************************************
    ?>
<div id="qo-dialog-confirm" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('mql_sentence_unit') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <p id="qo-dialog-text"></p>
      </div>
      <div class="modal-footer">
        <button type="button" id="qo-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" id="qo-nobutton" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        <button type="button" id="qo-okbutton" class="btn btn-primary" data-dismiss="modal"><?= $this->lang->line('OK_button') ?></button>
      </div>
    </div>
  </div>
</div>

<script>
  function display_clock(seconds_display, minutes_display) {
    // convert the seconds and minute values to strings
    minutes_display_str = String(minutes_display);
    seconds_display_str = String(seconds_display);

    // if the seconds and minutes are single digits add a preceeding zero
    if(seconds_display_str.length < 2) {
      seconds_display_str = "0" + seconds_display_str;
    }
    if(minutes_display_str.length < 2) {
      minutes_display_str = "0" + minutes_display_str;
    }

    // build the display string
    let display_string = minutes_display_str + ":" + seconds_display_str;
    
    // update the clock value
    $('#time-left').text(display_string);

    // update the time on the seconds menu
    update_seconds_menu(seconds_display);
    $("#seconds-timer").val(seconds_display);

    // update the time on the minutes menu
    update_seconds_menu(minutes_display);

    // turn on the timer
    turn_on_timer();

  }

  function get_minutes(seconds_display, total_time_seconds) {
    let minutes_display = total_time_seconds - seconds_display;
    minutes_display = Math.floor(minutes_display / 60); 
    return minutes_display;
  }

  function get_seconds(total_time_seconds) {
    let seconds_display = total_time_seconds % 60;
    return seconds_display;
  }

  function update_seconds_menu(seconds_display) {
    let seconds_display_str = String(seconds_display);
    //console.log("SECONDS: ", seconds_display_str);
    $("#seconds-timer").val(seconds_display);
  }

  function update_minutes_menu(minutes_display) {
    let minutes_display_str = String(minutes_display);
    console.log("MINUTES: ", minutes_display_str);
    $("#minutes-timer").val(minutes_display_str);
  }

  function turn_on_timer(){
    $("#activate-timer-menu").val("on");
  }

  $(document).ready(function() {
    if(is_unlimited === false){
      let seconds_display = get_seconds(total_time_seconds);
      let minutes_display = get_minutes(seconds_display, total_time_seconds);
      
      // display the time on the clock
      display_clock(seconds_display, minutes_display);
    }
  })
  
</script>