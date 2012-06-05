$(function(){
    // var sampleTags = ['c++', 'java', 'php', 'coldfusion',
	// 'javascript', 'asp', 'ruby', 'python', 'c', 'scala', 'groovy',
	// 'haskell', 'perl', 'erlang', 'apl', 'cobol', 'go', 'lua'];


    // -------------------------------
    // Single field
    // -------------------------------
    $('#nomCategorie').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagCategorie.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomAuteur').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagAuteur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomEditeur').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagEditeur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomDistributeur').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagDistributeur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomIllustrateur').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagIllustrateur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });

    // -------------------------------
    // Pour la gestion des jeux
    // -------------------------------
    $('#nomCategorieJeu').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    //requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagCategorie.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomAuteurJeu').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    //requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagAuteur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomEditeurJeu').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    //requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagEditeur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomDistributeurJeu').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    //requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagDistributeur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomIllustrateurJeu').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    //requireAutocomplete: true,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagIllustrateur.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomJeu').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    limitTag: 1,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagnomjeu.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
    
    $('#nomVersion').tagit({
	    // availableTags: sampleTags,
	    // This will make Tag-it submit a single form value, as a
		// comma-delimited field.
	    singleField: true,
	    allowSpaces: true,
	    autoFocusFirst: true,
	    requireAutocomplete: true,
	    limitTag: 1,
	    
	    tagSource: function(search, showChoices) {
	        var that = this;
	        var filter = search.term.toLowerCase();
	        $.ajax({
	          url: "tagnomversion.php",
	          dataType: "json",
	          type: "POST",
	          data:  { filter: filter, param : $('#idJeu').val() } ,
	          success: function(choices) {
	            showChoices(that._subtractArray(choices, that.assignedTags()));
	            availableTags:choices;
	          }
	        });
	      }
    });
});
$(document).ready(function() {
	$("#rechercheAvancee").hide();
	
	$( "#buttonAvance" ).click(function() {
		var options = {};
		$( "#rechercheAvancee" ).toggle( 'blind',options, 500 );
    		return false;
		});
	
	$("#recherche").submit(function() {
		$("form#recherche input:text").each(function(){
			   if($(this).val()==""){
				   $(this).remove();
			   }
			});
		$("form#recherche select").each(function(){
			   if($(this).val()==""){
				   $(this).remove();
			   }
			});
    });
	//$("#resultat tr:nth-child(odd)").addClass("odd");
	
});
