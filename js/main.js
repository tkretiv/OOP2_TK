//wait for DOM ready
$(function() {



  /**
   * function to start a new game
   *
   */

  function startNewGame() {
    $(".storyEvent").html("<h2>Många många år sedan var en sådan utmaning..</h2>");
    $(".storyOptions").html('<button class="newGame">Välj din karaktär => </button>');

    //new game clickHandler
    // ******** NEW GAME ************
    $(".newGame").click(function() {
      selectCharacter();
   
    });
  }



  function selectCharacter() {
    //empty DOM "printing" areas
    $(".storyEvent").html('');
    $(".storyOptions").html('');

    //add some instructions and input fields (not a form!)
    $(".storyEvent").append("<h2>Vem är du?:</h2>");

    //character name input
    $(".storyOptions").append('<h3>Namn:</h3>');
    $(".storyOptions").append('<input type="text" id="characterName" placeholder="character name">');

    //find out available characters for chosen storyline
    var availableCharacters = ["gaolin","arnold","ivan"];
    // $available_classes = array("gaolin", "arnold", "ivan");
    //character class input
    $(".storyOptions").append('<h3>Din karakters typ:</h3>');
    //append each available player class as a radio input
    //(using the same name for all radio prevents duplicate selection)
    for (var i = 0; i < availableCharacters.length; i++) {
      $(".storyOptions").append('<input type="radio" value="'+availableCharacters[i]+'" name="characterClass"><label>'+availableCharacters[i]+'</label><br>');
    }
    //finally append start new game button
    $(".storyOptions").append('<button class="startNewGame">Låt oss börja spelet!</button>');

    //button clickhandler
    //******** Val is done ****************
    $(".startNewGame").click(function() {
      var characterName = $("#characterName").val();
      var characterClass = $(".storyOptions input[type='radio']:checked").val();
      //if no characterName of characterClass was recieved, notify user
      if (!characterName || !characterClass) {

      } else {
        //else start new game

         $.ajax({
          url: "php/start_game.php",
          dataType: "json",
          data: {
            playerChoice : {
            "name" : characterName,
            "class" : characterClass
             }
          },
          success:  function(data) {
            DataSpel = data;
            console.log("startNewGame success: ", DataSpel);
                 //character name input
             console.log("Player success: ", DataSpel.players[0].items);
                 $.ajax({
                  url: "php/get_challenge.php",
                  dataType: "json",
                  data: {
                    lastChallenge : -1
                  },
                  success:  function(data) {
                    console.log("get_challenge success: ", data);
                    CarryOutChallenge(DataSpel.players[0],data);
                  },
                  error: function(data) {
                    console.log("get_challenge error: ", data.responseText);
                  }
                
                });
              },
          error: function(data) {
            console.log("startNewGame error: ", data.responseText);
          }
        
  });

      // Get_Utmaning(-1,CarryOutChallenge(characterName, characterClass, data));
      }
    });
  }


  function CarryOutChallenge(myPlayer, ChallengeData) {

             $(".storyEvent").html('');
              $(".storyOptions").html('');
              var chName=myPlayer.name;
              var chClass= myPlayer.typeName;
              //add some instructions and input fields (not a form!)
              $(".storyEvent").append("<h2>Välkommen till utmaningen, "+chClass+" "+chName+"!</h2>");
              var ToolList='<ul>';

              for (var i = 0; i < myPlayer.items.length; i++) {
                 ToolList=ToolList+' <li>'+myPlayer.items[i]["description"]+'</li>';
               }
               ToolList=ToolList+"</ul>";
               $(".storyEvent").append("<p>Du har grejer: "+ToolList+"</p>");


       
              $(".storyOptions").append('<h2>Nu är det dags för:</h2> <h3>'+ChallengeData.challenge["description"]+'.</h3>');
               $(".image").html('<img src="'+ChallengeData.challenge["img"]+'">');

              $(".storyOptions").append('<button class="DoChallenge">Kör det!</button>');
              $(".storyOptions").append('<button class="TeamChallenge">Kör det med hjälp..</button>');
              $(".storyOptions").append('<button class="nextChallenge">Nästa utmaningen. Du ska betala 5 poäng!</button>');

     //button clickhandler

    //******** Val is done ****************
       $(".DoChallenge").click(function() {

     $.ajax({
          url: "php/do_challenge.php",
          dataType: "json",
          data: {
            challenge_instructions: {
              "teamUp" : false
            }
          },
          success: function (data) {
            console.log("DoChallenge success: ", data);
                        printEventData(data, ChallengeData.index);

          },
          error: function(data) {
            console.log("DoChallenge error: ", data.responseText);
          }
        });
  });

    $(".TeamChallenge").click(function() {

     $.ajax({           url: "php/do_challenge.php",
          dataType: "json",
          data: {
            challenge_instructions: {
              "teamUp" : true,
              "teamUpWith" : 1
            }
          },
          success: function(data) {
            console.log("TeamChallenge success: ", data);
            printEventData(data, ChallengeData.index);
          }, //playNextEvent,
          error: function(data) {
            console.log("TeamChallenge error: ", data.responseText);
          }
        });
   });

     $(".nextChallenge").click(function() {

     $.ajax({
                    url: "php/get_challenge.php",
                    dataType: "json",
                    data: {
                      lastChallenge : ChallengeData.index,
                      refuse: true
                    },
                    success:  function(data) {
                      console.log("get_challenge2 success: ", data);
                      CarryOutChallenge(myPlayer, data);
                    },
                    error: function(data) {
                      console.log("get_challenge2 error: ", data.responseText);
                    }
                  
                  });
  });

}

 
  function printEventData(eventData, lastchallenge_ind) {
    console.log("got eventData: ", eventData);

    //if event data is false, assume we are starting a new game
    if (eventData === false) {
      startNewGame();
      return;
    }
    //if event data is an empty array, assume we have completed the game
    else if (eventData.length === 0) {
      startOver();
      return;
    }

    //get rid of array from AJAX result
    // eventData = eventData[0];

    //empty DOM "printing" areas
    $(".storyEvent").html("");
    $(".storyOptions").html("");

    //then append event data to DOM
    if (eventData.result[0].typeName==eventData.playing[0].typeName || eventData.result[0].name=="Team1" ) {

        $(".storyEvent").append("<h2> Du vann! </h2>");
        $(".storyEvent").append("<p> Du har nu "+eventData.playing[0].success+" poäng.</p>");

    }
    else {
         $(".storyEvent").append("<h2> Du blev slaget av "+eventData.result[0].typeName+" "+eventData.result[0].name+"</h2>");
        $(".storyEvent").append("<p> "+eventData.result[0].name+" har nu "+eventData.result[0].success+" poäng.</p>");
        $(".storyEvent").append("<p> Du har nu "+eventData.playing[0].success+" poäng.</p>");

    }
     var NewGame=false;
    if (lastchallenge_ind==10) {
        
        NewGame=true;
        
               
    }

              $.ajax({
                    url: "php/thechampion.php",
                    dataType: "json",
                    success:  function(data) {
                      console.log("the champion.php success: ", data);
                      NewGame=whoWon( data);
                      console.log("New ",NewGame);
                     
                    },
                    error: function(data) {
                      console.log("the champion.php error: ", data.responseText);
                    }
                  
                  });
     
          if (!NewGame) {
           $(".storyOptions").append('<button class="startNextGame">Låt oss spela!</button>');
           console.log("BUTTON",NewGame);
           $(".startNextGame").click(function() {
          
              $.ajax({
                    url: "php/get_challenge.php",
                    dataType: "json",
                    data: {
                      lastChallenge : lastchallenge_ind
                    },
                    success:  function(data) {
                      console.log("get_challenge2 success: ", data);
                      CarryOutChallenge(eventData.playing[0], data);
                    },
                    error: function(data) {
                      console.log("get_challenge2 error: ", data.responseText);
                    }
                  
                  });
         
          });
          }
          else {
            $(".startNextGame").hide();
              // $(".storyEvent").append("<h2> Det var sista utmaningen.. </h2>");
              $(".storyEvent").append("<h2>Dina slutsummerad poäng:"+eventData.playing[0].success+" </h2>");
               $(".storyEvent").append("<h2>"+eventData.playing[1].name+" slutsummerad poäng:"+eventData.playing[1].success+" </h2>");
              $(".storyEvent").append("<h2>"+eventData.playing[2].name+" slutsummerad poäng:"+eventData.playing[2].success+" </h2>");
   
              $(".storyOptions").append('<button class="startNewGame">Låt oss spela en gång till!</button>');
              $(".startNewGame").click(function() {
              startNewGame();
            });
          }
       
     
      
  }



function whoWon (eData) {
      if (eData.winners.length>0) {
           $(".storyEvent").append("<h1> "+eData.winners[0]["typeName"]+" "+eData.winners[0]["name"]+" har vunnit!</h1>");
            $(".storyEvent").append("<p>Först plats: "+eData.winners[0]["typeName"]+" "+eData.winners[0]["name"]+" med "+eData.winners[0]["success"]+" poäng </p>");
            $(".storyEvent").append("<p>Andra plats: "+eData.player[0].name+" slutsummerad poäng:"+eData.player[0].success+" </p>");
            $(".storyEvent").append("<p>Sista plats: "+eData.player[1].name+" slutsummerad poäng:"+eData.player[1].success+" </p>");
   
          $(".startNextGame").hide();
           $(".storyOptions").append('<button class="startNewGame">Låt oss spela en gång till!</button>');
           
            $(".startNewGame").click(function() {
                startNewGame();
            });
            return true;
          }
        else {return false;}
        
}

  

  function startOver() {
    $(".storyEvent").html("<h2>You have completed the game!</h2>");
    $(".storyOptions").html('<button class="startOver">Start over</button>');

    //start over clickhandler
    $(".startOver").click(function() {
    //   $.ajax({
    //     url: "reset.php",
    //     dataType: "json",
    //     data: {
    //       startOver: 1
    //     },
    //     success: 
    startNewGame();
    //     error: function(data) {
    //       console.log("startOver error: ", data.responseText);
    //     }
    //   });
    });
  }

  //always call playNextEvent on DOMReady
  // playNextEvent();
  startNewGame();
});