//workflow for this: 
/**
 * 1. Get quotes from DB
 * 1.5 on user select change on lesson plan - get quote from DB for lesson 
 * 2. Run game on user interaction
 * 3. On game end/timer run-out, send a POST
 *    to DB and store values in new entry for user. 
 */


jQuery(document).ready(function($){

    // Enable Bootstrap Tooltips Components    
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });


    let TIME_LIMIT = 15;

    let current_lesson = "";

    //element selectors for game display
    let timer_text = $('.current-time');
    let timerbar_progress = $('#pf-timerBar__progress');
    let timerbar_text = $('#pf-timerBar__text').html(0 + " seconds");
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
                quoteSpanArray = $('.char-input');
                let breakCount = 0;
                quoteSpanArray.each(function() {
                    if ($(this).children().length > 0) {
                        if(breakCount == 1){
                            $(this).remove();
                            breakCount = 0;
                        } else {
                            breakCount++;
                        }
                    }
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
        //count number of line breaks and subtract it from the quote input to match the lengths
        let lineBreakCount = 0;

        quoteSpanArray.each(function(index) {
            let typedChar = curr_input_array[index];
            if ($(this).children().length > 0) {
                lineBreakCount++;
            }

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
                // errors++; // increment number of errors
            }
        });
       
        // // display the number of errors
        // error_text.text(total_errors + errors);
       
        // // update accuracy text
        // let correctCharacters = (characterTyped - (total_errors + errors));
        // accuracy = ((correctCharacters / characterTyped) * 100);
        // accuracy_text.text(Math.round(accuracy));
       
        // if current text is completely typed, finish the game. 
        if (curr_input.length == (current_quote.length - lineBreakCount)) {
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
        timerbar_text.html(0 + " seconds");
    }

    function resetValues() {
        // timeLeft = TIME_LIMIT;
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
        timer_text.text(timeElapsed + 's');
        timerbar_progress.css("width", "100%");
        error_text.text(0);
        timerbar_text.html(timeElapsed + " seconds");
        $('#current-wpm').text("--");
    }

    function updateTimer() {
        if (true) {
            timeLeft--;
            timeElapsed++;
            timer_text.text(timeElapsed+ "s");

            timeProgressWidth = (timeElapsed / TIME_LIMIT) * 100;
            // timerbar_progress.css("width", timeProgressWidth + "%");
            timerbar_text.html(timeElapsed + " seconds");
        } else {
            finishGame();
        }
    }

    /**
     * This just finishes the game. You'll need to handle the information storage in the DB using this. 
     */
    function finishGame() {
        //stop timer
        // let time_spent = TIME_LIMIT - timeLeft;
        let time_spent = timeElapsed;
        console.log('time spent: ' + time_spent);

                
        //calculate errors
        errors = 0;
        let typedWords = input_area.val().split(' ');
        let wordCount = typedWords.length;
        let correctWordCount = 0;
        let hasError = false;
        quoteSpanArray = $('.char-input');
        let typedArea = input_area.val().length;
        quoteSpanArray.each(function(index) {
            if (index == typedArea) {
                if (hasError){
                    errors++;
                } else {
                    correctWordCount++;
                }
                return false;
            }
            if ($(this).text() == " " || index == quoteSpanArray.length - 1 || $(this).children().length > 0) {
                if (hasError) {
                    // console.log('error counted');
                    errors++;
                    hasError = false;
                } else {
                    // console.log('correct word counted');
                    correctWordCount++;
                }
            } else if ($(this).hasClass('incorrect-char')){
                // console.log('incorrect word found');
                hasError = true;
            }length
        });
        console.log('what is the word count here? ' + wordCount);
        console.log('what are said words? ' + typedWords);
        console.log('what are the correct words, then? ' + correctWordCount);
        console.log('errors? ' + errors);
        accuracy = (correctWordCount / (correctWordCount + errors)) * 100;
        if (isNaN(accuracy)){
            accuracy = 0;
            errors = "N/A";
        }
        error_text.html(errors);
        accuracy_text.html(Math.round(accuracy));

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
    $('.start-button').on('click', function() {
        if ($(this).text() == "Start" || $(this).text() == 'Restart') {
            $(this).text("End");
            startGame();
        } else {
            finishGame();
        }
    });
    $('#lesson-input').on('input', processCurrentText);

    $('.random-button').on('click', function() {
        let button = $(this);
        button.prop('disabled', true);
        quote_text.text("");
        let randomCompetency = Math.floor(Math.random() * 6) + 1;
        let randomLesson = Math.floor(Math.random() * 15) + 1;
        let lessonArea;
        switch(randomCompetency) {
            case 1:
                lessonArea = "financial-reporting";
                break;
            case 2: 
                lessonArea = "management-accounting";
                break;
            case 3: 
                lessonArea = "taxation";
                break;
            case 4: 
                lessonArea = "assurance";
                break;
            case 5:
                lessonArea = "strategy-and-governance";
                break;
            case 6:
                lessonArea = "finance";
                break;
            default:
                break;
        }
        console.log(lessonArea + " " + randomLesson);
        $('select[name="competency-selector"]').val(lessonArea);
        $('select[name="level-selector"]').val(randomLesson);
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: "typingtest_select_lesson",
                competency: lessonArea,
                level: randomLesson
            },
            success: function(response) {
                console.log('res: ' + response);
                current_lesson = response;
                current_quote = current_lesson;
                //separate each char and make an element to individually style each one
                current_quote.split('').forEach(function(char, index) {
                    const charSpan = document.createElement('span');
                    charSpan.className = "char-input";
                    charSpan.innerText = char;
                    quote_text.append(charSpan);
                });
                quoteSpanArray = $('.char-input');
                let breakCount = 0;
                quoteSpanArray.each(function() {
                    if ($(this).children().length > 0) {
                        if ($(this).children().length > 0) {
                            if(breakCount == 1){
                                $(this).remove();
                                breakCount = 0;
                            } else {
                                breakCount++;
                            }
                        }
                    }
                });
                button.prop('disabled', false);
            },
            error: function(error) {
                alert(error);
                button.prop('disabled', false);
            }
        });
    });


    /**
     * This section handles the user selections and will update the quotes with the lesson selection passed in as a parameter
     */
    $('.lesson-selector select').on('change', function(){
        updateQuote();
        start_button.html("Start");
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