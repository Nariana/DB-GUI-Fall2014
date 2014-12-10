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
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
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
	// message holds the ingredients to query
	private String[] ingredients;
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
		ingredients = intent.getStringArrayExtra(MainActivity.EXTRA_MESSAGE);
		// set SeekBars and their TextViews
		sbc = (SeekBar) findViewById(R.id.calories);
		sbc.setProgress(5000);
		tvc = (TextView) findViewById(R.id.calsProgress);
		sbt = (SeekBar) findViewById(R.id.time);
		sbt.setProgress(1000);
		tvt = (TextView) findViewById(R.id.timeProgress);
		//note: get value with sbc.getProgress()
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
		adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, android.R.id.text1, ingredients);
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
	
	public String getStringFromUrl(String url) {
    	String string;
    	StringBuilder builder = new StringBuilder();
    	HttpClient client = new DefaultHttpClient();
    	HttpGet httpGet = new HttpGet(url);
    	try {
    		HttpResponse response = client.execute(httpGet);
    		StatusLine statusLine = response.getStatusLine();
    		int statusCode = statusLine.getStatusCode();
    		if (statusCode == 200) {
    			HttpEntity entity = response.getEntity();
    			InputStream stream = entity.getContent();
    			BufferedReader reader = new BufferedReader(new InputStreamReader(stream));
    			while ((string = reader.readLine()) != null) {
    				builder.append(string);
    			}
    		}
    		else {
    			Log.e("API", "error reading from file.");
    		}
    	}
    	catch (ClientProtocolException e) {
    		e.printStackTrace();
    	}
    	catch (IOException e) {
    		e.printStackTrace();
    	}
    	return builder.toString();
    }
    public void setUpAutoComplete() {
    	String string = getStringFromUrl("http://54.69.70.135/DB-GUI-Fall2014/api/index.php/getIngredient");
    	try {
    		List<String> ingredients = new ArrayList<String>();
    		JSONArray jsonIngredients = new JSONArray(string);
    		JSONArray jsonArray = new JSONArray();
    		Log.i("MainActivity - Parsing to Json", jsonIngredients.toString());
    		// populate ingredients list from the json object
    		for (int i = 0; i < jsonIngredients.length(); i++) {
    			jsonArray = jsonIngredients.getJSONArray(i);
    			ingredients.add(jsonArray.getString(0));
    		}
    		Log.i("MainActivity - Parsing Json", ingredients.toString());
    	}
    	catch (Exception e) {
    		e.printStackTrace();
    	}
    	
    }

    final class callAPI extends AsyncTask<Void, Void, Void> {
    	
		@Override
		protected Void doInBackground(Void... params) {
			setUpAutoComplete();
			return null;
		}
    }
}
