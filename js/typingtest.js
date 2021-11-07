//workflow for this: 
/**
 * 1. Get quotes from DB
 * 1.5 on user select change on lesson plan - get quote from DB for lesson 
 * 2. Run game on user interaction
 * 3. On game end/timer run-out, send a POST
 *    to DB and store values in new entry for user. 
 */


jQuery(document).ready(function($){
    let TIME_LIMIT = 90;

    let current_lesson = "";

    //element selectors for game display
    let timer_text = $('.current-time');
    let timerbar_progress = $('#pf-timerBar__progress');
    let timerbar_text = $('#pf-timerBar__text').html(TIME_LIMIT + " seconds");
    let accuracy_text = $('#current-accuracy');
    let error_text = $('#current-errors');
    let wpm_text = $('#current-wpm');
    let quote_text = $('#lesson-content');
    let input_area = $('#lesson-input');
    let wpm_group = $('#wpm');
    let errors_group = $('#errors');
    let accuracy_group = $('#accuracy');
    let start_button = $('.start-button');

    let timeLeft = TIME_LIMIT;
    let timeProgressWidth = (timeLeft / TIME_LIMIT) * 100;
    let timeElapsed = 0;
    let total_errors = 0;
    let errors = 0;
    let accuracy = 0;
    let characterTyped = 0;
    let current_quote = "";
    let timer = null;

    let darkmode_btn_lesson = $("#pf-lessonSelector__button--darkmode");
    let darkmode_btn_stats_top = $("#pf-statsProfile__button--darkmode-top");
    let darkmode_btn_stats_bottom = $("#pf-statsProfile__button--darkmode-bottom");

    //this will have to be re-worked to interact with the DB
    //on user selection of lessons
    function updateQuote() {
        quote_text.text("");
        
        let competency = $('select[name="competency-selector"]').val();
        let level = $('select[name="level-selector"]').val();

        console.log('comp: ' + competency + ' skill ' + level);
        
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: "typingtest_select_lesson",
                competency: competency,
                level: level
            },
            success: function(response) {
                current_lesson = response;

                current_quote = current_lesson;
                //separate each char and make an element to individually style each one
                current_quote.split('').forEach(function(char, index) {
                    const charSpan = document.createElement('span');
                    charSpan.className = "char-input";
                    charSpan.innerText = char;
                    quote_text.append(charSpan);
                });
            },
            error: function(error) {
                alert(error);
            }
        });
    }


    $('#lesson-input').on('keyup', function() {
        let textCount = $('#lesson-input').val().length;

        quoteSpanArray = $('.char-input');
        quoteSpanArray.each(function(index) {
            if (index == textCount) {
                $(this).addClass('char-cursor');
            } else {
                $(this).removeClass('char-cursor');
            }
        });
    });

    //get currently typed text by user
    /**
     * This function will: 
     * -Get current value of input box
     * -Color the characters of the quote text according to progress
     * -Calculate errors and accuracy
     */
    function processCurrentText() {

        // get current input text and split it
        curr_input = input_area.val();
        curr_input_array = curr_input.split('');

        // increment total characters typed
        characterTyped++;
       
        errors = 0;
       
        quoteSpanArray = $('.char-input');
        quoteSpanArray.each(function(index) {
            let typedChar = curr_input_array[index];

            // character not currently typed
            if (typedChar == null) {
                $(this).removeClass('correct-char');
                $(this).removeClass('incorrect-char');
            } 
            // correct character
            else if (typedChar == $(this).text()) {
                $(this).addClass('correct-char');
                $(this).removeClass('incorrect-char');
            } 
            // incorrect character
            else {
                $(this).addClass('incorrect-char');
                $(this).removeClass('correct-char');
                errors++; // increment number of errors
            }
        });
       
        // display the number of errors
        error_text.text(total_errors + errors);
       
        // update accuracy text
        let correctCharacters = (characterTyped - (total_errors + errors));
        accuracy = ((correctCharacters / characterTyped) * 100);
        accuracy_text.text(Math.round(accuracy));
       
        // if current text is completely typed, finish the game. 
        if (curr_input.length == current_quote.length) {
          finishGame();
          // clear the input area
        //   input_area.val("");
        }
    }

    /**
     * Starts the game.
     * So far this will: 
     * -Reset all values
     * -Update the quote text
     * -Create a new timer
     */

    function startGame() {
        input_area.prop('disabled', false);
        resetValues();
        updateQuote();
        clearInterval(timer);
        timer = setInterval(updateTimer, 1000);
        timerbar_text.html(TIME_LIMIT + " seconds");
    }

    function resetValues() {
        timeLeft = TIME_LIMIT;
        timeElapsed = 0;
        errors = 0;
        total_errors = 0;
        accuracy = 0;
        characterTyped = 0;
        quoteNo = 0;
        $('.profile-button').prop('disabled', false);
       
        input_area.val("");
        // quote_text.text('Click on the area below to start the game.');
        accuracy_text.text(100);
        timer_text.text(timeLeft + 's');
        timerbar_progress.css("width", "100%");
        error_text.text(0);
        timerbar_text.html(TIME_LIMIT + " seconds");
    }

    function updateTimer() {
        if (timeLeft > 0) {
            timeLeft--;
            timeElapsed++;
            timer_text.text(timeLeft + "s");

            timeProgressWidth = (timeLeft / TIME_LIMIT) * 100;
            timerbar_progress.css("width", timeProgressWidth + "%");
            timerbar_text.html(timeLeft + " seconds");
        } else {
            finishGame();
        }
    }

    /**
     * This just finishes the game. You'll need to handle the information storage in the DB using this. 
     */
    function finishGame() {
        //stop timer
        let time_spent = TIME_LIMIT - timeLeft;
        console.log('time spent: ' + time_spent);

        clearInterval(timer);
        // timerbar_progress.css("width", "100%");

        //disable inputs until restart is hit
        input_area.prop('disabled', true);
        // $('.profile-button').prop('disabled', true);

        //show finishing message
        quote_text.text("Click the Restart button to begin the exercise again.");

        //display restart button by changing Start button
        start_button.html("Restart");

        //calculate wpm
        wpm = Math.round((((characterTyped / 5) / timeElapsed) * 60));

        //update wpm text
        wpm_text.text(wpm);

        let results = {
            competency: $('select[name="competency-selector"]').val(),
            level: $('select[name="level-selector"]').val(),
            wpm: wpm,
            accuracy: accuracy,
            errors: errors,
            time_spent: time_spent
        }

        //store game in DB for records
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: "typingtest_store_test_results",
                results: results
            },
            success: function(e) {
                console.log('test data successfully stored');
            },
            error: function(e) {
                console.log('error: ' + JSON.stringify(e));
                alert("Error! Something went wrong. Contact your web administrator. Error: " + e);
            }
        });
    }

    //This handles the elements in the tool starting the game
    $('.start-button').on('click', startGame);
    $('#lesson-input').on('input', processCurrentText);


    /**
     * This section handles the user selections and will update the quotes with the lesson selection passed in as a parameter
     */
    $('.lesson-selector select').on('change', function(){
        updateQuote();
    });


    // Dark Theme
    function toggleDarkMode() {
        $("#pf-helpModal__container").toggleClass("pf-darkmode");
        $("#pf-typingTest__container").toggleClass("pf-darkmode");
        $("#user-profile-container").toggleClass("pf-darkmode");

        if ($("div[id*='container']").hasClass("pf-darkmode")) {
            $("#pf-lessonSelector__button--darkmode").text("Light Mode");
            $("#pf-statsProfile__button--darkmode-top").text("Light Mode");
            $("#pf-statsProfile__button--darkmode-bottom").text("Light Mode");
        } else {
            $("#pf-lessonSelector__button--darkmode").text("Dark Mode");
            $("#pf-statsProfile__button--darkmode-top").text("Dark Mode");
            $("#pf-statsProfile__button--darkmode-bottom").text("Dark Mode");
        }
    }
    darkmode_btn_lesson.on("click", toggleDarkMode);
    darkmode_btn_stats_top.on("click", toggleDarkMode);
    darkmode_btn_stats_bottom.on("click", toggleDarkMode);

});