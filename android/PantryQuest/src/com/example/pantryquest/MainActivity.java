/* 
 * MainActivity lets the user input ingredients via a 
 * TextEdit which, when the add button is pressed, adds
 * the entered string into an array of strings. When the
 * search button is pressed, this array is passed in an
 * intent to create a Results activity
 */
package com.example.pantryquest;

/* inclusions */
import java.util.ArrayList;
import java.util.List;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;

public class MainActivity extends Activity implements OnClickListener, OnItemClickListener {
	
	// bt is the button to add ingredients to the search query
	private Button bt;
	// bt2 is the button to create an intent for the results page and start it
	private Button bt2;
	// et is the edittext where the user inputs ingredients
	private EditText et;
	// searchInput is a list containing the inputed ingredients
	private List<String> searchInput = new ArrayList<String>();
	// the Results activity intent contains a String[] of the search queries
	public final static String EXTRA_MESSAGE = "com.example.PantryQuest.MESSAGE";
	// dwr is the Drawer Layout encompassing the other views
	private DrawerLayout dwr;
	// lv is the listView containing the submitted ingredients
	private ListView lv;
	// adapter is used to populate the list view
	private ArrayAdapter<String> adapter;

    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d("PQ", "onCreate() Log Message");
        setContentView(R.layout.activity_main);
        
        // set variables and create click listeners
    	et = (EditText) findViewById(R.id.edit_message);
        bt = (Button) findViewById(R.id.button);
        bt2 = (Button) findViewById(R.id.button2);
        dwr = (DrawerLayout) findViewById(R.id.drawer_layout);
        lv = (ListView) findViewById(R.id.listView);
        bt.setOnClickListener(this);
        bt2.setOnClickListener(this);
        lv.setOnItemClickListener(this);
        // create the array with searchInput to populate listView
        adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, android.R.id.text1, searchInput);
		lv.setAdapter(adapter);
    }
    
    public void onClick(View v) {
    	// on bt click adds et contents to searchInput List
    	if (v.getId() == R.id.button) {
    		searchInput.add(et.getText().toString());
    		Log.d("PQ", "Adding ingredient: " + et.getText());
    		et.setText("");
    		adapter.notifyDataSetChanged();
    	}
    	// on bt2 click go to results page
    	else if (v.getId() == R.id.button2) {
    		//go to results page
    		Intent intent = new Intent(this, Results.class);
    		// create an array from the searchInput list
    		if (searchInput.size() > 0) {
    			String[] inputArray = searchInput.toArray(new String[searchInput.size()]);
        		intent.putExtra(EXTRA_MESSAGE, inputArray);
        		// call the results activity
        		startActivity(intent);
    		}
    	}
    }

    @Override
    protected void onStart() {
    	super.onStart();
    	Log.d("PQ", "MainActivity onStart() Log Message");
    }
    
   @Override
    protected void onResume() {
    	super.onResume();
    	Log.d("PQ", "MainActivity onResume() Log Message");
    }
    
   @Override
    protected void onPause() {
    	super.onPause();
    	Log.d("PQ", "MainActivity onPause() Log Message");
    }
   
   @Override
    protected void onStop() {
    	super.onStop();
    	Log.d("PQ", "MainActivity onStop() Log Message");
    }
   
   @Override
    protected void onRestart() {
    	super.onRestart();
    	Log.d("PQ", "MainActivity onRestart() Log Message");
    }
   
   @Override
    protected void onDestroy() {
    	super.onDestroy();
    	Log.d("PQ", "MainActivity onDestroy() Log Message");
    }

   	// remove searchInput element on click
	@Override
	public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
		searchInput.remove(position);
		adapter.notifyDataSetChanged();
	}
}
