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
	// root URL for the server
	private final static String rootUrl =  "the root url of the server";
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_results);
		Intent intent = getIntent();
		message = intent.getStringArrayExtra(MainActivity.EXTRA_MESSAGE);
		/*
		 * Make The Database Call Here
		 */
		
		/*
		 * 
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

/* Implemmentation based on a guide availble at:
 * http://blog.strikeiron.com/bid/73189/Integrate-a-REST-API-into-Android-Application-in-less-than-15-minutes
 * */

final class CallAPIResult {
	//contains the result
	//What data type does the request return?
}
final class CallAPI extends AsyncTask<String, String, String> {
	@Override
	protected String doInBackground(String... params) {
		String urlString = params[0]; //this is the url to call
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