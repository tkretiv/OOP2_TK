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
      // $.ajax({
      //   url: "php/start_game.php",
      //   dataType: "json",
      //   // data: {
      //   //   game_id : 0
      //   // },
      //   success: function(data) {
      //     console.log("NewGame success: ", data);
      //     selectCharacter(data);
      //   },
      //   error: function(data) {
      //     console.log("NewGame error: ", data.responseText);
      //   }
      // });
    });
  }



  /**
   * function to play the next chapter event
   *
   */

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
    $(".storyOptions").append('<button class="startNewGame">Låt oss spela!</button>');

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
                    CarryOutChallenge(DataSpel.players[0], characterClass,data);
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


  function CarryOutChallenge(myPlayer, characterClass, ChallengeData) {

             $(".storyEvent").html('');
              $(".storyOptions").html('');
              var chName=myPlayer.name;
              var chClass= characterClass;
              //add some instructions and input fields (not a form!)
              $(".storyEvent").append("<h2>Välkommen till utmaningen, "+chClass+" "+chName+"!</h2>");
              var ToolList='';
              
              for (var i = 0; i < myPlayer.items.length; i++) {
                 ToolList=ToolList+' '+myPlayer.items[i]["description"];
               }
               $(".storyEvent").append("<p>Du har haft grejer: "+ToolList+"!</p>");


        
              $(".storyOptions").append('<h3>Nu är det dags för:'+ChallengeData.challenge["description"]+'.</h3>');
                 
              $(".storyOptions").append('<button class="DoChallenge">Kör det!</button>');
              $(".storyOptions").append('<button class="TeamChallenge">Kör det med hjälp..</button>');
              $(".storyOptions").append('<button class="nextChallenge">Nästa utmaningen.</button>');

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
                        printEventData(data);

          },
          error: function(data) {
            console.log("DoChallenge error: ", data.responseText);
          }
        });
  });

    $(".TeamChallenge").click(function() {

     $.ajax({
          url: "php/do_challenge.php",
          dataType: "json",
          data: {
            challenge_instructions: {
              "teamUp" : true,
              "teamUpWith" : 1
            }
          },
          success: function(data) {
            console.log("TeamChallenge success: ", data);
            printEventData(data);
          }, //playNextEvent,
          error: function(data) {
            console.log("TeamChallenge error: ", data.responseText);
          }
        });
   });

}

  /**
   * function to play the next chapter event
   *
   */

  function playNextEvent() {
    //get current chapter event data from play_chapter.php
    $.ajax({
      url: "start_game.php",
      dataType: "json",
      success: printEventData,
      error: function(data) {
        console.log("playNextEvent error: ", data.responseText);
      }
    });
  }



  /**
   * function to print event data to DOM
   *
   */

  function printEventData(eventData) {
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
    eventData = eventData[0];

    //empty DOM "printing" areas
    $(".storyEvent").html("");
    $(".storyOptions").html("");

    //then append event data to DOM
    $(".storyEvent").append("<h2>"+eventData.title+"</h2>");
    $(".storyEvent").append("<p>"+eventData.description+"</p>");

    //then print event options
    var eventOptions = eventData.options;
    for (var i = 0; i < eventOptions.length; i++) {
      //create a new button using jQuery
      var option = $('<button>'+eventOptions[i].name+'</button>');
      //attach option data using jQuery .data()
      option.data("option", eventOptions[i]);

      //then append option button to DOM
      $(".storyOptions").append(option);
    }

    //add option clickHandler
    $(".storyOptions button").click(function() {
      //get action data from button .data()
      var thisOption = $(this).data("option");

      //then do the action!
      doOption(thisOption);
    });
  }



  /**
   * function to do an option selected by user
   *
   */

  function doOption(option) {
    $.ajax({
      url: "do_option.php",
      dataType: "json",
      data: {
        option: option
      },
      success: printDoOptionLog,
      error: function(data) {
        console.log("doOption error: ", data.responseText);
      }
    });
  }



  /**
   * function to print doOption log to DOM
   *
   */

  function printDoOptionLog(doOptionData) {
    console.log("doOptionData: ", doOptionData);
    //empty DOM "printing" areas
    $(".storyEvent").html("");
    $(".storyOptions").html("");

    for (var i = 0; i < doOptionData.rewards.length; i++) {
      $(".storyEvent").append("<h3>"+doOptionData.rewards[i]+"</h3>");
    }

    $(".storyOptions").append("<button>Play next event...</button>");

    //add option clickHandler
    $(".storyOptions button").click(function() {
      //get action data from button .data()
      playNextEvent();
    });
  }


  /**
   * function to start over
   *
   */

  function startOver() {
    $(".storyEvent").html("<h2>You have completed the game!</h2>");
    $(".storyOptions").html('<button class="startOver">Start over</button>');

    //start over clickhandler
    $(".startOver").click(function() {
      $.ajax({
        url: "reset.php",
        dataType: "json",
        data: {
          startOver: 1
        },
        success: startNewGame,
        error: function(data) {
          console.log("startOver error: ", data.responseText);
        }
      });
    });
  }

  //always call playNextEvent on DOMReady
  // playNextEvent();
  startNewGame();
});