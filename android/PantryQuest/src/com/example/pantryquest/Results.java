/*
 * When the Results activity is created, it uses the 
 * passed String[] to make a database request. The received
 * information is then used to populate a ListView. When
 * an item in the ListView is clicked, an intent for a 
 * Recipe activity using the respective recipe information
 * is created.
 */
/* A sliding fragment containing filters needs to be implemented! */
package com.example.pantryquest;

/* inclusions */
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;

public class Results extends Activity implements OnClickListener, OnItemClickListener{

	// bt is the button that, when pressed, returns to MainActivity
	private Button bt;
	// lv is the listview containing the ingredient search results
	private ListView lv;
	// adapter is used to populate the list view
	private ArrayAdapter<String> adapter;
	// message holds the query results
	private String[] message;
	// the Recipe activity intent contains a String[] of relevent info for the recipe
	public final static String RECIPE_INFO = "com.example.PantryQuest.RECIPE_INFO";
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_results);
		Intent intent = getIntent();
		message = intent.getStringArrayExtra(MainActivity.EXTRA_MESSAGE);
		/*
		 * Make The Database Call Here
		 */
		// create on click listener for bt
		bt = (Button) findViewById(R.id.backButton);
		bt.setOnClickListener(this);
		// populate the ListView
		lv = (ListView) findViewById(R.id.listView);
		lv.setOnItemClickListener(this);
		// !need to create a String[] just for the Titles of the recipes when
		// actually implementing this part.
		adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, android.R.id.text1, message);
		lv.setAdapter(adapter);
	}
	
	public void onClick(View v) {
		// on bt click return to MainActivity
		if (v.getId() == R.id.backButton) {
			this.finish();
		}
	}
	
	// on click listener for the ListView
	@Override
	public void onItemClick(AdapterView<?> parent, View view, int position,
			long id) {
		// create a Recipe activity intent and include all the recipe info inside it
		Intent intent = new Intent(this, Recipe.class);
		
		/*
		 * Make a String[] of recipe info to send
		 */
		String[] info;
		//intent.putExtra(RECIPE_INFO, info);
	}
	
	

}

/* Implemment the REST api */