<?php 
/**
* Plugin Name: Typing Test
* Plugin URI: N/A 
* Description: Typing test feature for CPA exam students at https://gevorgcpa.com/
* Version: 1.0.0
* Requires at least: 5.7.2
* Requires PHP: 7.0
* Author: Phil Fiess Software Development
* License: GPL v2 or later 
* License URI: https://www.gnu.org/livenses/gpl-2.0.html 
* Text Domain: GPVC
 */

//exits if directly accessed
if ( ! defined( 'ABSPATH' ) ){
    exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

/* Admin page */
function typingtest_register_settings() {
    add_menu_page("Typing Test", "Typing Test", "edit_posts", "typingtest_settings_menu", "typingtest_admin_page", null, null);
}

//admin section with all the lesson plans and text fields you can edit and set each lesson plan
function typingtest_admin_page() {
    global $wpdb;

    //update lesson content to DB
    if (isset($_POST['competency']) && isset($_POST['level']) && isset($_POST['lesson-content'])) {
        $competency = $_POST['competency'];
        $level = $_POST['level'];
        $lesson_content = $_POST['lesson-content'];

        $pattern = '/[^\w\s$%()[\]\-\*\/\:;<>=+@^#&.,!?\'"]/';
        $lesson_content_parsed = preg_replace($pattern, "", $lesson_content);

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

        //adds value if none there, updates if there is. 
        $sql = "SELECT level_$level FROM $table_name";
        $result = $wpdb->get_results($sql, ARRAY_A);
        if (!isset($result) || empty($result)) {
            $inserted = $wpdb->insert($table_name, array("level_$level" => $lesson_content_parsed));
        } else {
            $wpdb->update($table_name, array("level_$level" => $lesson_content_parsed), array('id' => 1));
        }
    }
?>

    <div id="typingtest-admin-wrapper">
        <h1 id="typingtest-admin-header">Gevorg, CPA Typing Test Admin</h1>
        <h2 id="typingtest-admin-form-title">Choose a competency and lesson to edit:</h2>
        <form method="POST" action="#" id="lessons-form">
            <label for="competency" class="select-label">Select Competency</label>
            <select name="competency" id="competency-selector">
                <option value="financial-reporting">Financial Reporting</option>
                <option value="management-accounting">Management Accounting</option>
                <option value="taxation">Taxation</option>
                <option value="assurance">Assurance</option>
                <option value="strategy-and-governance">Strategy and Governance</option>
                <option value="finance">Finance</option>
            </select>
            <label for="level" class="select-label">Select Lesson</label>
            <select name="level" id="difficulty-selector">
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
            <label for="lesson-content">Input Lesson Content</label>
            <textarea name="lesson-content" id="typingtest-admin-lesson-content" rows="20" cols="70"></textarea>
            <button type="submit" id="typingtest-admin-submit">Update Lesson</button>
        </form>
    </div>

<?php
}
add_action('admin_menu', 'typingtest_register_settings');


/* Creates necessary tables on plugin activation. 1 table per competency area * 10 lessons = 60 lessons, then one for the scores */
function typingtest_db_init() {
    global $wpdb;

    $table_prefix = $wpdb->prefix;

    //lesson tables
    $financial_reporting_lesson_table = $table_prefix . "typingtest_financial_reporting_lessons";
    $management_accounting_lesson_table = $table_prefix . "typingtest_management_accounting_lessons";
    $taxation_lesson_table = $table_prefix . "typingtest_taxation_lessons";
    $assurance_lesson_table = $table_prefix . "typingtest_assurance_lessons";
    $strategy_and_governance_lesson_table = $table_prefix . "typingtest_strategy_and_governance_lessons";
    $finance_lesson_table = $table_prefix . "typingtest_finance_lessons";
    
    //scores for student reporting
    $scores_table = $table_prefix . "typingtest_scores";

    $lesson_tables = array($financial_reporting_lesson_table, $finance_lesson_table, $management_accounting_lesson_table, $taxation_lesson_table, $assurance_lesson_table, $strategy_and_governance_lesson_table);

    if (! function_exists('maybe_create_table')){
        require_once ABSPATH . 'wp-admin/install-helper.php';
    }

    foreach ($lesson_tables as $table){
        $sql = "CREATE TABLE $table ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, level_1 TEXT NOT NULL DEFAULT '', level_2 TEXT NOT NULL DEFAULT '', level_3 TEXT NOT NULL DEFAULT '', level_4 TEXT NOT NULL DEFAULT '', level_5 TEXT NOT NULL DEFAULT '', level_6 TEXT NOT NULL DEFAULT '', level_7 TEXT NOT NULL DEFAULT '', level_8 TEXT NOT NULL DEFAULT '', level_9 TEXT NOT NULL DEFAULT '', level_10 TEXT NOT NULL DEFAULT '', level_11 TEXT NOT NULL DEFAULT '', level_12 TEXT NOT NULL DEFAULT '', level_13 TEXT NOT NULL DEFAULT '', level_14 TEXT NOT NULL DEFAULT '', level_15 TEXT NOT NULL DEFAULT '')";
        maybe_create_table($table, $sql);
    }

    $sql = "CREATE TABLE $scores_table ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id int NOT NULL, user_firstname VARCHAR(30) NOT NULL DEFAULT '', user_lastname VARCHAR(30) NOT NULL DEFAULT '', test_date DATETIME DEFAULT CURRENT_TIMESTAMP, competency_area varchar(40) NOT NULL, lesson_level INT NOT NULL, words_per_minute INT NOT NULL DEFAULT 0, typing_errors INT NOT NULL DEFAULT 0, accuracy INT NOT NULL DEFAULT 0, time_spent int not null default 0 )";
    maybe_create_table($scores_table, $sql);
}
register_activation_hook( __FILE__, 'typingtest_db_init' );