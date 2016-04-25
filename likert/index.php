<!--

Team Einerlei
File Written by: Isaac Meisner

This file generate the likert scale section of the questionaire.
There are currently 157 questions that each have 1-5 radio buttons
with a N/A option. Ten questions are displayed on a page and 
the next button submits the answers and the next ten questions 
appear asynchronously.

-->

<?php
session_start();
require '../src/timeout.php';
if(!isset($_SESSION['id']))
{
  header('Location: ../please-login/');
  exit();
}
if(!isset($_SESSION['demo_complete']))
{
  header('Location: ../demographics/');
  exit();
}
if(time() - $_SESSION["time"] > $timeout)
{
  session_unset();
  session_destroy();
  header('Location: ../please-login/');
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Performance Anxiety Diagostic Questionnaire</title>
<link rel="stylesheet" href="../style.css">
<script src="../src/jquery-1.12.2.min.js"></script>
</head>
<body>
<section>
  <img src='../images/einerlei_publishing_site001005.png'>
</section>
<div id="container">
<div id="content">
<script>


// JQUERY
//---------------------

var totalQues = 157;
var quesPerPage = 10;
var quesTxt = "question";
var totalPageNum = Math.ceil(totalQues / quesPerPage);
var pageNum = 0;
var obj;

//-------------------------------------------------------

$(document).ready(function(){
  $("#likert").append("<form id='quiz' method='post'>");

  //Loads Questions from json file into obj
  $.getJSON("likertQuestions.json", function(data)
  {
    obj = data;
    outputQuestions(obj, pageNum, 1, quesPerPage);
    $("#quiz").on("submit", function()
    {
      event.preventDefault();
      onSubmit(obj);
    }); 
  });

  $("#likert").append("</form>");
});

//-------------------------------------------------------

function outputQuestions(obj, pageNum, firstQues, quesOnPage)
{
  //alert("quesOnPage="+quesOnPage);
  for(quesNum=firstQues; quesNum<quesOnPage+firstQues; quesNum++)
  {
    var quesWithNum = "question"+quesNum;
    var quesTxt = obj[quesWithNum];
    $("#quiz").append("<p id='question" + quesNum + "'>" + quesTxt + "<br></p>");
    for(option=1; option<=6; option++)
    {
      if(option == 6)
      {
        $("[id='question"+quesNum+"']").append("<div class='checkboxgroup'>"
            + "<input type='radio' required  id='answer"+quesNum+"."+option+"'"
            + "name='"+quesTxt+"' value='"+option+"'/>"
            + "<label for='answer"+quesNum+"."+option+"'>N/A</label></div>");
      }
      else
      {
        $("[id='question"+quesNum+"']").append("<div class='checkboxgroup'>"
            + "<input type='radio' required  id='answer"+quesNum+"."+option+"'"
            + "name='"+quesTxt+"' value='"+option+"'/>"
            + "<label for='answer"+quesNum+"."+option+"'>"+option
            + "</label></div>");
      }
    }
  }
  if(quesOnPage == totalQues % quesPerPage)
  {
    $("#quiz").append("<button id='confirm' type='submit' value='submit'>Submit</button>");
    $("#quiz").append("<progress max='"+totalPageNum+"' value='"
                                       +pageNum+"'></progress>");
  }else
  {
    $("#quiz").append("<button id='confirm' type='submit' value='submit'>Next</button>");
    $("#quiz").append("<progress max='"+totalPageNum+"' value='"
                                       +pageNum+"'></progress>");
  }  
}

//-------------------------------------------------------

function onSubmit(obj)
{
  if(typeof onSubmit.counter == 'undefined')
  {onSubmit.counter = 11;}

  if(typeof onSubmit.pageNum == 'undefined')
  {onSubmit.pageNum = 1;}

  var quesOnLast = totalQues % quesPerPage;

  if(quesNum == ((totalQues - quesOnLast)+1))
  {
    //alert("Last Page!");
    quesOnPage = quesOnLast;
    submitAnswers(quesNum-quesPerPage, quesPerPage);
    $("#quiz").empty();
    outputQuestions(obj, onSubmit.pageNum, onSubmit.counter, quesOnPage);
    window.scrollTo(0,0);
    onSubmit.counter += quesOnPage;
    onSubmit.pageNum += 1;
  }else if(quesNum > totalQues)
  {
    //alert("Finished!");
    submitAnswers(quesNum-quesOnLast, quesOnLast);
    window.location = "../finish/";
  }else
  {
    quesOnPage = quesPerPage;
    submitAnswers(quesNum-quesPerPage, quesPerPage);
    $("#quiz").empty();
    outputQuestions(obj, onSubmit.pageNum, onSubmit.counter, quesOnPage);
    window.scrollTo(0,0);
    onSubmit.counter += quesOnPage;
    onSubmit.pageNum += 1;
  }
}


//-------------------------------------------------------
// This function submits the answers from the previous
// questions to the questionSubmit.php file 
// Parameters:
//    -firstQues: the first question number
//    -numOfQs: how many questions are on the page 
//
//-------------------------------------------------------

function submitAnswers(firstQues, numOfQs)
{
  var counter = 0;
  var args = "";
  for(quesNum=firstQues; quesNum<firstQues+numOfQs; quesNum++)
  {
    for(option=1; option<=6; option++)
    {
      var ansWithVal = "answer"+quesNum+"."+option;
      var idName = $("[id='"+ansWithVal+"']").attr("name");
      var qNum = "q" + counter;
      var aNum = "a" + counter;
      if($("[id='"+ansWithVal+"']").is(':checked'))
      {
        args += qNum + "=" + idName + "&"+ aNum + "=" + $("[id=\""+ansWithVal+"\"]").val(); 
        counter++;
      }
    }
    if(quesNum != firstQues+(quesPerPage-1))
    {
      args += "&";
    }
  }
  $.post("questionSubmit.php", args);
  //alert(args);
}

//-------------------------------------------------------


</script>

<h1>Likert Scale Questions</h1>
<h4>For the following questions, please answer with a number between 1 and 5.</h4>
<ul>
  <li>1 "Strongly Disagree"</li>
  <li>2 "Disagree"</li>
  <li>3 "Undecided" or "Neutral"</li>
  <li>4 "Agree"</li>
  <li>5 "Strongly Agree"</li>
</ul>
<h4 id="bottom">Select the appropriate number.  </h4>

<div id="likert">
</div>
</div>
</div>
</body>
</html>
