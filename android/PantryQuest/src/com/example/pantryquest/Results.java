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
import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;
import org.xmlpull.v1.XmlPullParserFactory;

import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ListView;
import android.widget.SeekBar;
import android.widget.SeekBar.OnSeekBarChangeListener;
import android.widget.TextView;

public class Results extends Activity implements OnClickListener, OnItemClickListener, OnSeekBarChangeListener 	{

	// bt is the button that, when pressed, returns to MainActivity
	private Button bt;
	// bt2 is the button that, when pressed, refreshes the search
	private Button bt2;
	// lv is the listview containing the ingredient search results
	private ListView lv;
	// adapter is used to populate the list view
	private ArrayAdapter<String> adapter;
	// message holds the query results
	private String[] message;
	// the Recipe activity intent contains a String[] of relevent info for the recipe
	public final static String RECIPE_INFO = "com.example.PantryQuest.RECIPE_INFO";
	// root URL for the server
	private final static String rootUrl =  "the root url of the server";
	// all of the filter checkboxes
	private CheckBox cb_bake, cb_boil, cb_grill, cb_slowcook, cb_stovetop, cb_fry,
		cb_lactose, cb_vegetarian, cb_vegan,cb_gluten, cb_nonuts;
	// sbc is the SeekBar for the max calories selection
	private SeekBar sbc;
	// tvc is the TextView which will display the progress of sbc
	private TextView tvc;
	// sbt is the SeekBar for the max preptime
	private SeekBar sbt;
	// tvt is the TextView which will display the progress of sbt
	private TextView tvt;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_results);
		Intent intent = getIntent();
		message = intent.getStringArrayExtra(MainActivity.EXTRA_MESSAGE);
		// set SeekBars and their TextViews
		sbc = (SeekBar) findViewById(R.id.calories);
		sbc.setProgress(5000);
		tvc = (TextView) findViewById(R.id.calsProgress);
		sbt = (SeekBar) findViewById(R.id.time);
		sbt.setProgress(1000);
		tvt = (TextView) findViewById(R.id.timeProgress);
		//note: geet value with sbc.getProgress()
		// set CheckBox objects to their respective view
		cb_bake = (CheckBox) findViewById(R.id.cb_bake);
		cb_boil = (CheckBox) findViewById(R.id.cb_boil);
		cb_grill = (CheckBox) findViewById(R.id.cb_grill);
		cb_slowcook = (CheckBox) findViewById(R.id.cb_slowcook);
		cb_stovetop = (CheckBox) findViewById(R.id.cb_stovetop);
		cb_fry = (CheckBox) findViewById(R.id.cb_fry);
		cb_lactose = (CheckBox) findViewById(R.id.cb_lactose);
		cb_vegetarian = (CheckBox) findViewById(R.id.cb_vegetarian);
		cb_vegan = (CheckBox) findViewById(R.id.cb_vegan);
		cb_gluten = (CheckBox) findViewById(R.id.cb_gluten);
		cb_nonuts = (CheckBox) findViewById(R.id.cb_nonuts);
		//note: get value with cb_boil.isEnabled()
		/*
		 * Make The Database Call Here
		 */
		
		/*
		 * 
		 */
		// create listener for the SeekBars
		sbc.setOnSeekBarChangeListener(this);
		sbt.setOnSeekBarChangeListener(this);
		// create on click listener for bt
		bt = (Button) findViewById(R.id.backButton);
		bt.setOnClickListener(this);
		bt2 = (Button) findViewById(R.id.refreshButton);
		bt2.setOnClickListener(this);
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
		else if (v.getId() == R.id.refreshButton) {
			// refresh search
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

   	// to handle displaying the progress of the SeekBars
	@Override
	public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
		if (seekBar.getId() == R.id.calories) {
			tvc.setText("Max Calories: " +progress);
		}
		else if (seekBar.getId() == R.id.time) {
			tvt.setText("Max PrepTime (Minutes): " +progress);
		}
	}
	
	@Override
	public void onStartTrackingTouch(SeekBar seekBar) {}
	
	@Override
	public void onStopTrackingTouch(SeekBar seekBar) {}
}

/* Implemmentation based on a guide availble at:
 * http://blog.strikeiron.com/bid/73189/Integrate-a-REST-API-into-Android-Application-in-less-than-15-minutes
 * */

final class CallAPIResult {
	//contains the result
	//Probably a String[] that contains cycling recipe info, 
}
final class CallAPI extends AsyncTask<String, String, String> {
	@Override
	protected String doInBackground(String... params) {
		String urlString = "http://localhost:8888/api/index.html"; //this is the url to call
		String resultToDisplay = "";
		InputStream in = null;
		CallAPIResult result = null;
		
		// Http GET
		try {
			URL url = new URL(urlString);
			HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
			in = new BufferedInputStream(urlConnection.getInputStream());
		}
		catch (Exception e) {
			Log.e("API", e.getMessage());
			return e.getMessage();
		}
		
		// Parse XML
		XmlPullParserFactory pullParserFactory;
		try {
			pullParserFactory = XmlPullParserFactory.newInstance();
			XmlPullParser parser = pullParserFactory.newPullParser();
			parser.setFeature(XmlPullParser.FEATURE_PROCESS_NAMESPACES, false);
			parser.setInput(in, null);
			result = parseXml(parser);
		}
		catch (XmlPullParserException e) {
			e.printStackTrace();
		}
		catch (IOException e) {
			e.printStackTrace();
		}
		
		return resultToDisplay;
	}
	
	private CallAPIResult parseXml(XmlPullParser parser) throws XmlPullParserException, IOException {
		int eventType = parser.getEventType();
		CallAPIResult result = new CallAPIResult();
		while (eventType != XmlPullParser.END_DOCUMENT) {
			String name = null;
			switch (eventType) 
			{
			case XmlPullParser.START_TAG:
				name = parser.getName();
				if (name == "Error") {
					Log.e("API", "Web API Error!");
				}
				break;
			case XmlPullParser.END_TAG:
				break;
			}
			eventType = parser.next();
		}
		return result;
	}
}

