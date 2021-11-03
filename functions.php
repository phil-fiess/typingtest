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
    return '<div class="typingtest-container">
                <div class="typingtest-heading">
                    CPA Typing Speed Tester
                </div>

                <div class="lesson-selector">
                    <label for="competency-selector" class="competency-selector-label">Select A Competency Section</label>
                    <select name="competency-selector" id="tool-competency-selector">
                        <option value="financial-reporting">Financial Reporting</option>
                        <option value="management-accounting">Management Accounting</option>
                        <option value="taxation">Taxation</option>
                        <option value="assurance">Assurance</option>
                        <option value="strategy-and-governance">Strategy and Governance</option>
                        <option value="finance">Finance</option>
                    </select>
                    <label for="level-selector" class="level-selector-label">Select A Difficulty Level</label>
                    <select name="level-selector" id="tool-difficulty-selector">
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
                    <div class="buttons-wrapper">
                        <button class="button start-button">Start</button>    
                        <button class="button restart-button">Restart</button>
                        <button class="button profile-button">User Profile</button>
                        <button class="button help-button">Help</button>
                    </div>
                </div>

                <div class="typingtest-header">
                    <div class="wpm">
                        <div class="header-text">WPM</div>
                        <div class="current-wpm">100</div>
                    </div>
                    <div class="errors">
                        <div class="header-text">Errors</div>
                        <div class="current-errors">0</div>
                    </div>
                    <div class="timer">
                        <div class="header-text">Time</div>
                        <div class="current-time">60s</div>
                    </div>
                    <div class="accuracy">
                        <div class="header-text">% Accuracy</div>
                        <div class="current-accuracy">100</div>
                    </div>
                </div>

                <div class="lesson-wrapper">
                    <div class="lesson-content" spellcheck="false">
                        Select a section and difficulty level, then hit start to begin the exercise.
                    </div>
                    <textarea class="input-area" spellcheck="false"></textarea>
                </div>
            </div>
            
            <div id="user-profile-container">
                <div id="user-profile-basic-info">
                    <div class="profile-info-section">
                        <h2>Stats For:</h2>
                        <h3 id="user-name"></h3>
                        <h4 id="user-email"></h4>
                        <h4 id="subscription-info"></h4>
                        <h4 id="total-time-alltime"></h4>
                    </div>
                </div>

                <div id="stats-section">
                    <div class="alltime-stats-info-section">
                        <h2 class="stats-title">All Time Statistics</h2>
                        <h3 class="stats-header">Total Lessons</h3>
                        <h5 class="stats-value total-lessons"></h5>
                        <h3 class="stats-header">Total Time</h3>
                        <h5 class="stats-value total-time"></h5>
                        <h3 class="stats-header">Top Speed</h3>
                        <h5 class="stats-value top-speed"></h5>
                        <h3 class="stats-header">Average Speed</h3>
                        <h5 class="stats-value average-speed"></h5>
                    </div>
                    <div class="today-stats-info-section">
                        <h2 class="stats-title">Today\'s Statistics</h2>
                        <h3 class="stats-header">Total Lessons</h3>
                        <h5 class="stats-value total-lessons"></h5>
                        <h3 class="stats-header">Total Time</h3>
                        <h5 class="stats-value total-time"></h5>
                        <h3 class="stats-header">Top Speed</h3>
                        <h5 class="stats-value top-speed"></h5>
                        <h3 class="stats-header">Average Speed</h3>
                        <h5 class="stats-value average-speed"></h5>
                    </div>
                </div>

                <div id="graphs-section">
                    <div id="relative-speed">
                        <canvas id="relative-speed-chart" width="800" height="800"></canvas>
                    </div>
                    <div id="speed-per-competency">
                        <canvas id="speed-per-competency-chart" width="900" height="700"></canvas>
                    </div>
                </div>
                <button class="typinglessons-button">Back to Lessons</button>
                <button class="help-button">Help</button>    
            </div>';
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
        $user_financial_reporting_speed /= $user_total_financial_reporting_games;
        $user_taxation_speed /= $user_total_taxation_games;
        $user_management_accounting_speed /= $user_total_management_accounting_games;
        $user_assurance_speed /= $user_total_assurance_games;
        $user_finance_speed /= $user_total_finance_games;
        $user_strategy_and_governance_speed /= $user_total_strategy_and_governance_games;

        //speed for class for relative charts
        $class_financial_reporting_speed /= $total_financial_reporting_games;
        $class_taxation_speed /= $total_taxation_games;
        $class_management_accounting_speed /= $total_management_accounting_games;
        $class_assurance_speed /= $total_assurance_games;
        $class_finance_speed /= $total_finance_games;
        $class_strategy_and_governance_speed /= $total_strategy_and_governance_games;

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