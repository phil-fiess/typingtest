<?php

function add_scripts_and_styles() {
    wp_enqueue_script('admin-display-lesson.js', plugins_url('/js/admin-display-lesson.js', __FILE__), array('jquery', 'json2'));
    wp_localize_script('admin-display-lesson.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_script('typingtest.js', plugins_url('/js/typingtest.js', __FILE__), array('jquery', 'json2'));
    wp_localize_script('typingtest.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('main.css', plugins_url('/styles/css/main.css', __FILE__));
    wp_enqueue_script('chart.js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js', array(), null, true);
    wp_enqueue_script('typingtest-charts.js', plugins_url('/js/typingtest-charts.js', __FILE__), array('jquery', 'json2'));
    wp_localize_script('typingtest-charts.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    // wp_enqueue_script('js/scripts.js', plugins_url('/js/scripts.js', __FILE__));

    wp_enqueue_script('bootstrap.bundle.min.js', plugins_url('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', __FILE__));
    wp_enqueue_style('bootstrap.min.css', plugins_url('node_modules/bootstrap/dist/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('popper.min.js', plugins_url('node_modules/popper.js/dist/popper.min.js', __FILE__));

}
add_action('init', 'add_scripts_and_styles');


//ajax handler to send back data to front end 
//to display lesson content in admin section
function typingtest_display_admin_lesson() {
    global $wpdb;
    if (isset($_POST['competency']) && isset($_POST['level'])) {
        $competency = $_POST['competency'];
        $level = $_POST['level'];
        
        switch($competency) {
            case "financial-reporting":
                $table_name = $wpdb->prefix . "typingtest_financial_reporting_lessons";
                break;
            case "management-accounting":
                $table_name = $wpdb->prefix . "typingtest_management_accounting_lessons";
                break;
            case "taxation":
                $table_name = $wpdb->prefix . "typingtest_taxation_lessons";
                break;
            case "assurance":
                $table_name = $wpdb->prefix . "typingtest_assurance_lessons";
                break;
            case "strategy-and-governance":
                $table_name = $wpdb->prefix . "typingtest_strategy_and_governance_lessons";
                break;
            case "finance":
                $table_name = $wpdb->prefix . "typingtest_finance_lessons";
                break;
        }
        $sql = "SELECT level_$level FROM $table_name";
        $result = $wpdb->get_results($sql, ARRAY_A);
        if (isset($result[0]["level_$level"])) {
            echo $result[0]["level_$level"];
        } else {
            echo "insert your lesson content here";
        }

        wp_die();
    }
}
add_action('wp_ajax_typingtest_display_admin_lesson', 'typingtest_display_admin_lesson');
add_action('wp_ajax_nopriv_typingtest_display_admin_lesson', 'typingtest_display_admin_lesson');

/**Typing Test Tool Shortcode. This renders the UI for the typing test. Just place it on a page and you're set. */
function typingtest_render_tool_shortcode() { 
    return '
        <div class="pf-container container-fluid" id="pf-helpModal__container"> 
            <div class="modal" id="pf-helpModal__wrapper" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title">Instructions</p>
                            <button class="btn-close modal-button-close" id="pf-helpModal__button--x" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <button class="pf-button--rounded button" id="pf-helpModal__buttonStartExercise" type="button" data-bs-toggle="collapse" data-bs-target="#pf-helpModal__collapseStartExercise" aria-expanded="false" aria-controls="pf-helpModal__collapseStartExercise">Starting An Exercise</button>
                            <div class="collapse" id="pf-helpModal__collapseStartExercise">
                                <p class="pf-helpModal__paragraph"><b>Step 1:</b> Select a <mark>Competency Section</mark> from the drop-down box.</p>
                                    <p class="pf-helpModal__detail">The app will offer sentences related to the competency area chosen.</p>
                                    <p class="pf-helpModal__detail"><b>Example:</b> The Financial Reporting section will have related sentences from topics such as revenue recognition, leases, financial instruments, inventories, impairment, provision, contingent liability, etc.</p>
                                    <p class="pf-helpModal__detail"><b>Note:</b> If you are in Core 1, I recommend to choose mostly Financial Reporting; in Core 2, choose Management Accounting; in Elective Modules, choose the competency area that matches your module.</p>
                                <p class="pf-helpModal__paragraph"><b>Step 2:</b> Select a <mark>Difficulty Level</mark> from the drop-down box.</p>
                                    <p class="pf-helpModal__detail">Available levels are 1-15, with 1 being the easiest and 15 being the most difficult. The higher the chosen level, the more complex the sentences will be.</p>
                                <p class="pf-helpModal__paragraph"><b>Step 3:</b> Click the <mark>Start</mark> button.</p>
                                    <p class="pf-helpModal__detail">As you\'re typing, the app will measure the following info:</p>
                                    <ul class="pf-helpModal__detail--list">
                                        <li><b>Typing Speed:</b> measured as words-per-minute (WPM). Calculated using standard WPM formula: (Characters typed / 5 ) / (Time elapsed). To give an example, if you typed 200 characters in 1 minute, your WPM typing speed would be (200 characters / 5) / 1 min = 40 WPM. The recommended typing speed for CPA exams is 40-45 WPM.</li>
                                        <li><b>Time:</b> how many seconds spent until the exercise was completed</li>
                                        <li><b>Errors:</b> the number of words mistyped</li>
                                        <li><b>Accuracy:</b> the percentage of letters typed accurately in comparison to the total letters in the exercise</li>
                                    </ul>
                                <p class="pf-helpModal__paragraph"><b>Step 4:</b> Once the exercise is complete, your exercise stats will display in the three boxes.</p>
                                <p class="pf-helpModal__paragraph"><b>Step 5:</b> To re-do the lesson, click the <mark>Restart</mark> button; to select a new lesson, choose a competency section and difficulty level then click the <mark>Start</mark> button.</p>
                            </div>

                            <button class="pf-button--rounded button" id="pf-helpModal__buttonAccessProfile" type="button" data-bs-toggle="collapse" data-bs-target="#pf-helpModal__collapseAccessProfile" aria-expanded="false" aria-controls="pf-helpModal__collapseAccessProfile">Accessing User Profile & Stats</button>
                            <div class="collapse" id="pf-helpModal__collapseAccessProfile">
                                <p class="pf-helpModal__paragraph">Your lesson statistics can be viewed by clicking the <mark>User Profile</mark> button</p>
                                    <ul class="pf-helpModal__detail--list">
                                        <li><b>Total Statistics:</b> All statistics since account creation.</li>
                                        <li><b>Today\'s Statistics:</b> The statistics since 12:00am local time</li>
                                    </ul>
                                <p class="pf-helpModal__paragraph">You can also view data charts and graphs below your statistics.</p>
                                    <ul class="pf-helpModal__detail--list">
                                        <li><b>Relative Typing Speed:</b> This radar chart shows your stats compared with all other registered students. The highlighted areas are your WPM per each competency area. Lines closer to the outer edges represents a higher score. Lines closer to the centre represent a lower score.</li>
                                        <li><b>Average Typing Speed:</b> This bar chart shows your average typing speed per competency area.</li>
                                    </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <p>If you have any questions, concerns or problems, please reach out for help: <b>support@gevorgcpa.com</b><p>
                            <button class="btn btn-secondary" id="pf-helpModal__button--close" type="button" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pf-container container-fluid typingtest-container" id="pf-typingTest__container">

            <div class="lesson-selector">
                <div class="selectors-wrapper">

                    <div class="selector-label-wrapper">
                        <label class="selector-label competency-selector-label" for="competency-selector">Select Competency</label>
                        <select class="selector" name="competency-selector" id="tool-competency-selector">
                            <option value="financial-reporting">Financial Reporting</option>
                            <option value="management-accounting">Management Accounting</option>
                            <option value="taxation">Taxation</option>
                            <option value="assurance">Assurance</option>
                            <option value="strategy-and-governance">Strategy and Governance</option>
                            <option value="finance">Finance</option>
                        </select>
                    </div>
                    <div class="selector-label-wrapper">
                        <label class="selector-label level-selector-label" for="level-selector">Select Lesson</label>
                        <select class="selector" name="level-selector" id="tool-difficulty-selector">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                        </select>
                    </div>
                </div>
                <div class="pf-button__wrapper">
                    <div class="buttons-wrapper pf-button__wrapper--one" id="">
                        <button class="button start-button">Start</button>
                    </div>
                    <div class="buttons-wrapper pf-button__wrapper--one id="">
                        <button class="button random-button">Random Lesson</button>
                    </div>
                    <div class="buttons-wrapper pf-button__wrapper--one" id="pf-lessonSelect__button--userprofile">
                        <button class="button profile-button">User Profile</button>
                    </div>
                    <div class="buttons-wrapper pf-button__wrapper--one" id="">
                        <button class="button" id="pf-lessonSelector__button--darkmode" data-bs-toggle="" data-bs-target="">Dark Mode</button>
                    </div>
                    <div class="buttons-wrapper pf-button__wrapper--one" id="">
                        <button class="button help-button" id="pf-lessonSelector__button--help" data-bs-toggle="modal" data-bs-target="#pf-helpModal__wrapper">Help</button>
                    </div>
                </div>
            </div>

            <div class="lesson-wrapper">
                <div class="typingtest-statsbar">
                    <div class="statsbox" id="wpm" data-bs-toggle="tooltip" data-bs-placement="top" title="Typing speed measured in words-per-minute (WPM)">
                        <div class="statsbox-label">WPM</div>
                        <div class="statsbox-current" id="current-wpm">--</div>
                    </div>
                    <div class="statsbox" id="errors" data-bs-toggle="tooltip" data-bs-placement="top" title="The number of words mistyped">
                        <div class="statsbox-label">Errors</div>
                        <div class="statsbox-current" id="current-errors">--</div>
                    </div>
                    <div class="statsbox" id="accuracy" data-bs-toggle="tooltip" data-bs-placement="top" title="The percentage of words typed accurately in comparison to the total letters in the exercise">
                        <div class="statsbox-label">Accuracy</div>
                        <div class="statsbox-current" id="current-accuracy">--</div>
                    </div>
                </div>
                <div class="pf-timerbar" id="pf-timerBar__wrapper">
                    <div class="pf-timerbar" id="pf-timerBar__base">
                        <div class="pf-timerbar" id="pf-timerBar__progress"></div>
                        <div class="pf-timerbar" id="pf-timerBar__text"></div>
                    </div>
                </div>
                <div id="lessontext-wrapper">
                    <div class="lesson-text" id="lesson-content" spellcheck="false">
                        <span>Select a Compentency and Lesson, then click the START button to begin.</span>
                    </div>
                    <textarea class="lesson-text" id="lesson-input" spellcheck="false"></textarea>
                </div>
            </div>
        </div>

        <div class="pf-container container-fluid" id="user-profile-container">

            <div class="wrapper-buttons">
                <button class="button typinglessons-button">Back to Lessons</button>
                <button class="button help-button" data-bs-toggle="modal" data-bs-target="#pf-helpModal__wrapper">Help</button>
                <button class="button" id="pf-statsProfile__button--darkmode-top" data-bs-toggle="" data-bs-target="">Dark Mode</button>
            </div>  

            <div class="wrapper">
                <div id="user-profile-basic-info">
                    <div class="profile-info-section">
                        <h2 class="heading-profile-stats" id="heading_userprofile">User Profile</h2>
                        <h4 class="label-profile" id="label-user-name">Name </h4>
                        <p class="output-profile" id="user-name"></h3>
                        <h4 class="label-profile" id="label-user-email">Email </h4>
                        <p class="output-profile" id="user-email"></h4>
                        <h4 class="label-profile" id="label-user-subscription">Subscription </h4>
                        <p class="output-profile" id="subscription-info"></h4>
                    </div>
                </div>
                <div class="stats-info-section today-stats-info-section" id="today_statsInfoSection">
                    <h2 class="heading-profile-stats stats-title">Today\'s Statistics</h2>
                    <div class="wrapper-stats">
                        <h4 class="label-stats stats-header">Total Lessons </h4>
                        <p class="value-stats total-lessons"></p>
                        <h4 class="label-stats stats-header">Total Time </h4>
                        <p class="value-stats total-time"></p>
                        <h4 class="label-stats stats-header">Top Speed </h4>
                        <p class="value-stats top-speed"></p>
                        <h4 class="label-stats stats-header">Average Speed </h4>
                        <p class="value-stats average-speed"></p>
                    </div>
                </div>
                <div class="stats-info-section alltime-stats-info-section">
                    <h2 class="heading-profile-stats stats-title">Total Statistics</h2>
                    <div class="wrapper-stats">
                        <h4 class="label-stats stats-header">Total Lessons </h4>
                        <p class="value-stats total-lessons"></p>
                        <h4 class="label-stats stats-header">Total Time </h4>
                        <p class="value-stats total-time"></p>
                        <h4 class="label-stats stats-header">Top Speed </h4>
                        <p class="value-stats top-speed"></p>
                        <h4 class="label-stats stats-header">Average Speed </h4>
                        <p class="value-stats average-speed"></p>
                    </div>
                </div>
            </div>

            <div id="graphs-section">
                <div class="graph-div-wrapper" id="relative-speed">
                    <canvas class="graph-canvas" id="relative-speed-chart" width="600" height="600"></canvas>
                </div>
                <div class="graph-div-wrapper" id="speed-per-competency">
                    <canvas class="graph-canvas" id="speed-per-competency-chart" width="600" height="600"></canvas>
                </div>
            </div>
            <div class="wrapper-buttons">
                <button class="button typinglessons-button">Back to Lessons</button>
                <button class="button help-button" data-bs-toggle="modal" data-bs-target="#pf-helpModal__wrapper">Help</button>
                <button class="button" id="pf-statsProfile__button--darkmode-bottom" data-bs-toggle="" data-bs-target="">Dark Mode</button>
            </div>


            

        </div>
        ';
}
add_shortcode('typingtest_tool', 'typingtest_render_tool_shortcode');

/**
 * Handles request for typing test to return the lesson
 * users select to tool
 */

function typingtest_select_lesson() {
    global $wpdb;
    if (isset($_POST['competency']) && isset($_POST['level'])) {
        $competency = $_POST['competency'];
        $level = $_POST['level'];
        
        switch($competency) {
            case "financial-reporting":
                $table_name = $wpdb->prefix . "typingtest_financial_reporting_lessons";
                break;
            case "management-accounting":
                $table_name = $wpdb->prefix . "typingtest_management_accounting_lessons";
                break;
            case "taxation":
                $table_name = $wpdb->prefix . "typingtest_taxation_lessons";
                break;
            case "assurance":
                $table_name = $wpdb->prefix . "typingtest_assurance_lessons";
                break;
            case "strategy-and-governance":
                $table_name = $wpdb->prefix . "typingtest_strategy_and_governance_lessons";
                break;
            case "finance":
                $table_name = $wpdb->prefix . "typingtest_finance_lessons";
                break;
        }
        $sql = "SELECT level_$level FROM $table_name";
        $result = $wpdb->get_results($sql, ARRAY_A);
        if (isset($result[0]["level_$level"])) {
            echo $result[0]["level_$level"];
        } else {
            echo "No lesson here. Select another!";
        }
        wp_die();
    }
}
add_action('wp_ajax_typingtest_select_lesson', 'typingtest_select_lesson');
add_action('wp_ajax_nopriv_typingtest_select_lesson', 'typingtest_select_lesson');

function typingtest_store_test_results() {
    global $wpdb;
    
    $results = $_POST['results'];
    $id = get_current_user_id();
    $fname = get_user_meta($id, 'first_name', true);
    $lname = get_user_meta($id, 'last_name', true);
    $competency = $results['competency'];
    $level = $results['level'];
    $wpm = $results['wpm'];
    $accuracy = $results['accuracy'];
    $errors = $results['errors'];
    $time_spent = $results['time_spent'];

    $table = 'wp_typingtest_scores';
    $data = array(
        'user_id' => $id,
        'user_firstname' => $fname,
        'user_lastname' => $lname,
        'competency_area' => $competency,
        'lesson_level' => $level,
        'words_per_minute' => $wpm,
        'typing_errors' => $errors,
        'accuracy' => $accuracy,
        'time_spent' => $time_spent
    );
    $format = array('%s', '%d');
    $wpdb->insert($table, $data, $format);

    echo "data stored successfully";
    wp_die();
}
add_action('wp_ajax_typingtest_store_test_results', 'typingtest_store_test_results');
add_action('wp_ajax_nopriv_typingtest_store_test_results', 'typingtest_store_test_results');

function typingtest_retrieve_profile() {
    global $wpdb;

    $table = $wpdb->prefix . "typingtest_scores";

    $id = get_current_user_id();
    $fname = get_user_meta($id, 'first_name', true);
    $lname = get_user_meta($id, 'last_name', true);
    $userinfo = get_userdata($id);
    $email = $userinfo->user_email;


    //top headings values
    $user_total_time_alltime = 0;
    $user_total_time_today = 0;
    $user_total_lessons_alltime = 0;
    $user_total_lessons_today = 0;
    $user_top_speed_alltime = 0;
    $user_top_speed_today = 0;
    $user_average_speed_alltime = 0;
    $user_average_speed_today = 0;

    //speed for user for relative charts
    $user_financial_reporting_speed = 0;
    $user_taxation_speed = 0;
    $user_management_accounting_speed = 0;
    $user_assurance_speed = 0;
    $user_finance_speed = 0;
    $user_strategy_and_governance_speed = 0;

    //speed for class for relative charts
    $class_financial_reporting_speed = 0;
    $class_taxation_speed = 0;
    $class_management_accounting_speed = 0;
    $class_assurance_speed = 0;
    $class_finance_speed = 0;
    $class_strategy_and_governance_speed = 0;

    //calculates average games played for each task
    $total_financial_reporting_games = 0;
    $total_taxation_games = 0;
    $total_management_accounting_games = 0;
    $total_assurance_games = 0;
    $total_strategy_and_governance_games = 0;
    $total_finance_games = 0;

    $user_total_financial_reporting_games = 0;
    $user_total_taxation_games = 0;
    $user_total_management_accounting_games = 0;
    $user_total_assurance_games = 0;
    $user_total_strategy_and_governance_games = 0;
    $user_total_finance_games = 0;

    //get all results from DB
    $sql = "SELECT * FROM $table";
    $results = $wpdb->get_results($sql);

    //calculate all of the values needed for individual user
    if (isset($results) && !empty($results)) {
        foreach($results as $result) {
            //compound all individual values first, or add class data
            if($result->user_id == $id) {
                $user_total_lessons_alltime += 1;
                $user_total_time_alltime += $result->time_spent;
                if ($result->words_per_minute > $user_top_speed_alltime){
                    $user_top_speed_alltime = $result->words_per_minute;
                }
                //this will be calculated out after the loop
                $user_average_speed_alltime += $result->words_per_minute; 
                //I NEED TO BE DONE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!//
                //TODAY'S SCORES NEED TO BE COMPUTED!!!!!!!!!!!!!!!!!//

                $today = date('Y-m-d');
                $test_date_time = strtotime($result->test_date);
                $test_date = date('Y-m-d', $test_date_time);
                if (strtotime($today) == strtotime($test_date)) {
                    $user_total_time_today += $result->time_spent;
                    $user_total_lessons_today += 1;
                    if ($result->words_per_minute > $user_top_speed_today){
                        $user_top_speed_today = $result->words_per_minute;
                    }
                    $user_average_speed_today += $result->words_per_minute;
                }



                //speed for user for relative charts
                switch($result->competency_area) {
                    case "financial-reporting":
                        $user_financial_reporting_speed += $result->words_per_minute;
                        $user_total_financial_reporting_games++;
                        $total_financial_reporting_games++;
                        break;
                    case "management-accounting":
                        $user_management_accounting_speed += $result->words_per_minute;
                        $user_total_management_accounting_games++;
                        $total_management_accounting_games++;
                        break;
                    case "taxation":
                        $user_taxation_speed += $result->words_per_minute;
                        $user_total_taxation_games++;
                        $total_taxation_games++;
                        break;
                    case "assurance":
                        $user_assurance_speed += $result->words_per_minute;
                        $user_total_assurance_games++;
                        $total_assurance_games++;
                        break;
                    case "strategy-and-governance":
                        $user_strategy_and_governance_speed += $result->words_per_minute;
                        $user_total_strategy_and_governance_games++;
                        $total_strategy_and_governance_games++;
                        break;
                    case "finance":
                        $user_finance_speed += $result->words_per_minute;
                        $user_total_finance_games++;
                        $total_finance_games++;
                        break;
                    default:
                        break;
                }
                //speed for class for relative charts
                $class_financial_reporting_speed += $user_financial_reporting_speed;
                $class_taxation_speed += $user_taxation_speed;
                $class_management_accounting_speed += $user_management_accounting_speed;
                $class_assurance_speed += $user_assurance_speed;
                $class_finance_speed += $user_finance_speed;
                $class_strategy_and_governance_speed += $user_strategy_and_governance_speed;
            } else {
                //speed for user for relative charts
                switch($result->competency_area) {
                    case "financial-reporting":
                        $class_financial_reporting_speed += $result->words_per_minute;
                        $total_financial_reporting_games++;
                        break;
                    case "management-accounting":
                        $class_management_accounting_speed += $result->words_per_minute;
                        $total_management_accounting_games++;
                        break;
                    case "taxation":
                        $class_taxation_speed += $result->words_per_minute;
                        $total_taxation_games++;
                        break;
                    case "assurance":
                        $class_assurance_speed += $result->words_per_minute;
                        $total_assurance_games++;
                        break;
                    case "strategy-and-governance":
                        $class_strategy_and_governance_speed += $result->words_per_minute;
                        $total_strategy_and_governance_games++;
                        break;
                    case "finance":
                        $class_finance_speed += $result->words_per_minute;
                        $total_finance_games++;
                        break;
                    default:
                        break;
                }
            } 
        }

        // calculate average speeds for each values
        //speed for user for relative charts
        if ($user_total_financial_reporting_games != 0) {
            $user_financial_reporting_speed /= $user_total_financial_reporting_games;
        } else {
            $user_financial_reporting_speed = 0;
        }
        if ($user_total_taxation_games != 0) {
            $user_taxation_speed /= $user_total_taxation_games;
        } else {
            $user_taxation_speed = 0;
        }
        if ($user_total_management_accounting_games != 0) {
            $user_management_accounting_speed /= $user_total_management_accounting_games;
        } else {
            $user_management_accounting_speed = 0;
        }
        if ($user_total_assurance_games != 0) {
            $user_assurance_speed /= $user_total_assurance_games;
        } else {
            $user_assurance_speed = 0;
        }
        if ($user_total_finance_games != 0) {
            $user_finance_speed /= $user_total_finance_games;
        } else {
            $user_finance_speed = 0;
        }
        if ($user_total_strategy_and_governance_games != 0) {
            $user_strategy_and_governance_speed /= $user_total_strategy_and_governance_games;
        } else {
            $user_strategy_and_governance_speed = 0;
        }

        //speed for class for relative charts
        if ($total_financial_reporting_games != 0) {
            $class_financial_reporting_speed /= $total_financial_reporting_games;
        } else {
            $class_financial_reporting_speed = 0;
        }
        if ($total_taxation_games != 0) {
            $class_taxation_speed /= $total_taxation_games;            
        } else {
            $class_taxation_speed = 0;
        }
        if ($total_management_accounting_games != 0) {
            $class_management_accounting_speed /= $total_management_accounting_games;            
        } else {
            $class_management_accounting_speed = 0;
        }
        if ($total_assurance_games != 0) {
            $class_assurance_speed /= $total_assurance_games;
        } else {
            $class_assurance_speed = 0;
        }
        if ($total_finance_games != 0) {
            $class_finance_speed /= $total_finance_games;
        } else {
            $class_finance_speed = 0;
        }
        if ($total_strategy_and_governance_games != 0) {
            $class_strategy_and_governance_speed /= $total_strategy_and_governance_games;
        } else {
            $class_strategy_and_governance_speed = 0;
        }

        //average speeds for top bar charts
        $user_average_speed_alltime /= count($results);
        if ($user_total_lessons_today != 0) {
            $user_average_speed_today /= $user_total_lessons_today;
        } else {
            $user_average_speed_today = 0;
        }

        //Parse it all into an associative array and send it back 
        $response = array(
            'user_data' => array(
                'profile'=> array(
                    'first_name' => $fname,
                    'last_name' => $lname,
                    'email' => $email
                ),
                'alltime' => array(
                    'total_lessons' => $user_total_lessons_alltime,
                    'total_time' => $user_total_time_alltime,
                    'top_speed' => $user_top_speed_alltime,
                    'average_speed' => $user_average_speed_alltime
                ),
                'today' => array(
                    'total_lessons' => $user_total_lessons_today,
                    'total_time' => $user_total_time_today,
                    'top_speed' => $user_top_speed_today,
                    'average_speed' => $user_average_speed_today
                ),
                'competencies' => array(
                    'financial_reporting' => $user_financial_reporting_speed,
                    'taxation' => $user_taxation_speed,
                    'management_accounting' => $user_management_accounting_speed,
                    'assurance' => $user_assurance_speed,
                    'strategy_and_governance' => $user_strategy_and_governance_speed,
                    'finance' => $user_finance_speed
                )
            ),
            'class_data' => array(
                'competencies' => array(
                    'financial_reporting' => $user_financial_reporting_speed,
                    'taxation' => $user_taxation_speed,
                    'management_accounting' => $user_management_accounting_speed,
                    'assurance' => $user_assurance_speed,
                    'strategy_and_governance' => $user_strategy_and_governance_speed,
                    'finance' => $user_finance_speed
                )
            )
        );

        echo json_encode($response);
        wp_die();
    } else {
        wp_send_json_error('There is nothing here to display', 404);
        wp_die();
    }
}
add_action('wp_ajax_typingtest_retrieve_profile', 'typingtest_retrieve_profile');
add_action('wp_ajax_nopriv_typingtest_retrieve_profile', 'typingtest_retrieve_profile');