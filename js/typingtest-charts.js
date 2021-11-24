jQuery(document).ready(function($) {

    function changeTextOnColourModeBtn() {
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


    $('.profile-button').on('click', function() {
        $('.typingtest-container').hide();
        $('#user-profile-container').show("slow", function() {
            //call data to get the results each time so it updates after lessons end
            $.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: {
                    action: "typingtest_retrieve_profile"
                },
                success: function(response) {
                    console.log('works: ' + JSON.stringify(response));
                    let profileData = JSON.parse(response);
                    new Chart($('#speed-per-competency-chart'), {
                        type: 'bar',
                        data: {
                            labels: ["Financial Reporting", "Management Accounting", "Taxation", "Assurance", "Strategy and Governance", "Finance"],
                            datasets: [
                                {
                                    label: "Average Speed (WPM)",
                                    backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850", "##166af2"],
                                    data: [
                                        Math.floor(profileData.user_data.competencies.financial_reporting),
                                        Math.floor(profileData.user_data.competencies.management_accounting),
                                        Math.floor(profileData.user_data.competencies.taxation), 
                                        Math.floor(profileData.user_data.competencies.assurance),
                                        Math.floor(profileData.user_data.competencies.strategy_and_governance),
                                        Math.floor(profileData.user_data.competencies.finance)
                                    ]
                                }
                            ]
                        },
                        options: {
                            legend: {display: false},
                            title: {
                                display: true,
                                text: "Average Typing Speed Per Competency Area"
                            }
                        }
                    });
                    new Chart($('#relative-speed-chart'), {
                        type: 'radar',
                        data: {
                            labels: ["Financial Reporting", "Management Accounting", "Taxation", "Assurance", "Strategy and Governance", "Finance"],
                            datasets: [
                                {
                                    label: "Your Speed",
                                    fill: true,
                                    backgroundColor: "rgba(179,181,198,0.2)",
                                    borderColor: "rgba(179,181,198,1)",
                                    pointBorderColor: "#fff",
                                    pointBackgroundColor: "rgba(179,181,198,1)",
                                    data: [
                                        Math.floor(profileData.user_data.competencies.financial_reporting),
                                        Math.floor(profileData.user_data.competencies.management_accounting),
                                        Math.floor(profileData.user_data.competencies.taxation), 
                                        Math.floor(profileData.user_data.competencies.assurance),
                                        Math.floor(profileData.user_data.competencies.strategy_and_governance),
                                        Math.floor(profileData.user_data.competencies.finance)
                                    ]
                                },
                                {
                                    label: "User Average",
                                    fill: true,
                                    backgroundColor: "rgba(255,99,132,0.2)",
                                    borderColor: "rgba(255,99,132,1)",
                                    pointBorderColor: "#fff",
                                    pointBackgroundColor: "rgba(255,99,132,1)",
                                    data: [
                                        Math.floor(profileData.class_data.competencies.financial_reporting),
                                        Math.floor(profileData.class_data.competencies.management_accounting),
                                        Math.floor(profileData.class_data.competencies.taxation), 
                                        Math.floor(profileData.class_data.competencies.assurance),
                                        Math.floor(profileData.class_data.competencies.strategy_and_governance),
                                        Math.floor(profileData.class_data.competencies.finance)
                                    ]
                                }
                            ]
                        },
                        options: {
                            title: {
                                display: true, 
                                text: "Relative Average Typing Speeds By Competency Area (WPM)"
                            }
                        }
                    });
                    //parse time into MM:SS format
                    let totalMinutes = Math.floor(profileData.user_data.alltime.total_time / 60);
                    let totalSeconds = profileData.user_data.alltime.total_time % 60;
                    let todayMinutes = Math.floor(profileData.user_data.today.total_time / 60);
                    let todaySeconds = profileData.user_data.today.total_time % 60;

                    //populate the top fields with the data
                    $('#user-name').text(profileData.user_data.profile.first_name + " " + profileData.user_data.profile.last_name);
                    $('#user-email').text(profileData.user_data.profile.email);
                    $('#total-time-alltime').text("Total time (all time)" + profileData.user_data.alltime.total_time);

                    $('.alltime-stats-info-section .total-lessons').text(profileData.user_data.alltime.total_lessons);
                    $('.alltime-stats-info-section .total-time').text(totalMinutes + ":" + totalSeconds);
                    $('.alltime-stats-info-section .top-speed').text(profileData.user_data.alltime.top_speed);
                    $('.alltime-stats-info-section .average-speed').text(Math.round((profileData.user_data.alltime.average_speed + Number.EPSILON) * 100) / 100);

                    $('.today-stats-info-section .total-lessons').text(profileData.user_data.today.total_lessons);
                    $('.today-stats-info-section .total-time').text(todayMinutes + ":" + todaySeconds);
                    $('.today-stats-info-section .top-speed').text(profileData.user_data.today.top_speed);
                    $('.today-stats-info-section .average-speed').text(Math.round((profileData.user_data.today.average_speed + Number.EPSILON) * 100) / 100);
                },
                error: function(e) {
                    console.log('error: ' + JSON.stringify(e));
                    alert("No profile information found!");
                }
            });
        });

        changeTextOnColourModeBtn();
    });

    $('.typinglessons-button').on('click', function() {
        $('#user-profile-container').hide();
        $('.typingtest-container').show("slow");

        changeTextOnColourModeBtn();
    })


});