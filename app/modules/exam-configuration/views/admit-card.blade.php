<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>School Management Software | Eton</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    

    <style>
    
    .cardBox{ padding:5px; border: 1px solid #333; font-family: arial; margin: 20px 5px }
    .sTitle{ font-size: 15px; font-weight: bold; text-align: center; line-height: 35px }
    .sLoc{ font-size: 13px; line-height: 20px; text-align: center; font-weight: bold; margin-bottom: 5px; }
    .sExam{ text-align: center; font-size: 18px; line-height: 25px; margin-bottom:5px; }
    .sType{ font-size: 15px; text-transform: uppercase; line-height: 25px; margin-bottom: 5px; border-bottom: 1px solid #333; text-align: center; font-weight: bold; }
    .det{ line-height: 30px }
    .sign{ text-align: center; margin-top: 30px; font-weight: bold; }

    .mainCard{ width: 350px; float: left }

    .cBlock{ float: left; margin-right: 25px; line-height: 20px }

    .holder{ padding-bottom: 10px; overflow: hidden; font-size: 12px !important }
    
    
    @media print {
      .pagebreak { page-break-after: always !important; }
      .mRight{float: right}

    }
    
    </style>

    <!-- jQuery 2.1.4 -->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
   <script  src="https://code.jquery.com/jquery-2.2.4.min.js"  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
   
    <script>
    $( document ).ready(function() {
      $(".sCard:nth-child(even)").addClass("nextDiv");
    });
  </script>
  </head>
  <body>
    <div class="container">
        <div id="admitCardRow" >
          <div id="admitCardTemplate" >
              <div class="mainCard">
                <div class="cardBox">
                    <div class="sTitle">{{ SettingsHelper::getGeneralSetting('long_school_name') }}</div>
                    <div class="sLoc">{{ SettingsHelper::getGeneralSetting('address') }}</div>
                    <div class="sExam">{{ $exam->exam_name }} - {{ AcademicSession::find($exam->session_id)->session_name }}</div>
                    <div class="sType">ADMIT CARD</div>
                      <div class="holder">
                        <div class="cBlock"><strong>Name:</strong> <%= student_name %> <!-- ExamConfigurate-v1-changes-made-here /////// --><%= last_name %> <!-- ExamConfigurate-v1-changes-made-here /////// --></div>
                        <div class="cBlock"><strong>Roll no:</strong><!-- ExamConfigurate-v1-changes-made-here /////// --> <%= current_roll_number %><!-- ExamConfigurate-v1-changes-made-here /////// --></div>
                      </div>
                      <div class="holder">
                        <div class="cBlock"><strong>Class:</strong> <%= class_name %></div>
                        <div class="cBlock"><strong>Section:</strong> <%= section_name %></div>
                      </div>
                      <div class="holder">
                          <div class="cBlock"><strong>Exam Start:</strong> {{ 
                            HelperController::formatNepaliDate(
                                (new DateConverter)->ad2bs($exam->exam_start_date_in_ad)
                              )
                            }}
                          </div>
                          <div class="cBlock"><strong>End:</strong>  {{ 
                            HelperController::formatNepaliDate(
                              (new DateConverter)->ad2bs($exam->exam_end_date_in_ad)
                              )
                            }}
                          </div>
                      </div>
                      <div class="sign">
                          ........................................ <br/>
                          Principal
                    </div>
                  </div>
                </div>
              </div><!-- card ends -->
          </div>        
        </div>
    </div>

    <script>
    $(function() {
      var data = {{ json_encode($data) }};
      
      console.log(data);
      
      var admitCardTemplate = $('#admitCardTemplate').html()
      admitCardTemplate = admitCardTemplate.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
      var compiledAdmitCardTemplate = _.template(admitCardTemplate);

      var admitCardHTML = '';
  var count = 1;
      for (var studentNumber = 0; studentNumber < data.length; studentNumber++) {
        if (!data[studentNumber]) {
          continue;
        }

        studentAdmitCard = compiledAdmitCardTemplate(data[studentNumber]);

        if (count % 6 == 0) {
          var newReport = $('<div>').attr('class', 'pagebreak');
          newReport.html('&nbsp;');
          admitCardHTML += studentAdmitCard;
          admitCardHTML += newReport[0].outerHTML;
        } else {
          admitCardHTML += studentAdmitCard;
        }
        count++;
      }
      $('#admitCardRow').html(admitCardHTML);
      // window.print();
      $( ".mainCard:odd" ).addClass( 'mRight' );
    });
    </script>
  </body>

</html>