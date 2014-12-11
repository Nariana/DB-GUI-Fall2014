/* 
 * MainActivity lets the user input ingredients via a 
 * TextEdit which, when the add button is pressed, adds
 * the entered string into an array of strings. When the
 * search button is pressed, this array is passed in an
 * intent to create a Results activity
 */
package com.example.pantryquest;

/* inclusions */
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.ExecutionException;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends Activity implements OnClickListener, OnItemClickListener {
	
	// bt is the button to add ingredients to the search query
	private Button bt;
	// bt2 is the button to create an intent for the results page and start it
	private Button bt2;
	// et is the edittext where the user inputs ingredients
	private AutoCompleteTextView et;
	// searchInput is a list containing the inputed ingredients
	private List<String> searchInput = new ArrayList<String>();
	// the Results activity intent contains a String[] of the search queries
	public final static String EXTRA_MESSAGE = "com.example.PantryQuest.MESSAGE";
	public static final String BASE_URL = "web/api_2/index.php/";
	// dwr is the Drawer Layout encompassing the other views
	private DrawerLayout dwr;
	// lv is the listView containing the submitted ingredients
	private ListView lv;
	// adapter is used to populate the list view
	private ArrayAdapter<String> adapter;
	// tvLogin is the text view which should change to display account name
	private TextView tvLogin;
	// these are the EditText views for entering username and password
	private EditText tUsr, tPass;
	// these are the buttons for logging in/out and for opening the register view
	private Button btLog, btOut, btReg;
	// these EditTexts are on the register page
	private EditText rName, rUsr, rPass;
	// this button is tied to submitting when registering
	private Button btRegSub;
	// this is the json array that holds the total list of ingredients
	private JSONArray jsonIngredients;
	// this is the list that contains the strings from ingredients
	private List<String> ingredients = new ArrayList<String>();
	// this ArrayAdapter populates the AutoCompleteTextView
	private ArrayAdapter<String> suggestions; 
	// this is a string that gets populated with search results
	private String results;
	// used for Toast messages

    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d("PQ", "onCreate() Log Message");
        setContentView(R.layout.activity_main);

    	et = (AutoCompleteTextView) findViewById(R.id.edit_message);
        bt = (Button) findViewById(R.id.button);
        bt2 = (Button) findViewById(R.id.button2);
        dwr = (DrawerLayout) findViewById(R.id.drawer_layout);
        lv = (ListView) findViewById(R.id.listView);
        tvLogin = (TextView) findViewById(R.id.welcome);
        tUsr = (EditText) findViewById(R.id.edit_username);
        tPass = (EditText) findViewById(R.id.edit_password);
        btLog = (Button) findViewById(R.id.bt_login);
        btReg = (Button) findViewById(R.id.bt_register);
        btOut = (Button) findViewById(R.id.bt_logout);
        rName = (EditText) findViewById(R.id.register_name);
        rUsr = (EditText) findViewById(R.id.register_username);
        rPass = (EditText) findViewById(R.id.register_password);
        btRegSub = (Button) findViewById(R.id.bt_regSubmit);
        
        bt.setOnClickListener(this);
        bt2.setOnClickListener(this);
        btLog.setOnClickListener(this);
        btReg.setOnClickListener(this);
        btOut.setOnClickListener(this);
        btRegSub.setOnClickListener(this);
        lv.setOnItemClickListener(this);
        
        // views hidden initially
        btOut.setVisibility(View.GONE);
        rName.setVisibility(View.GONE);
        rUsr.setVisibility(View.GONE);
        rPass.setVisibility(View.GONE);
        btRegSub.setVisibility(View.GONE);
        
        // set up the autocomplete textview
        callAPI a = new callAPI();
        try {
			a.execute().get();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		}
        suggestions = new ArrayAdapter<String>(this, android.R.layout.select_dialog_item, ingredients);
    	et.setAdapter(suggestions);
        // create the array with searchInput to populate listView
        adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, android.R.id.text1, searchInput);
		lv.setAdapter(adapter);
    }
    
    public void onClick(View v) {
    	Context context = getApplicationContext();
    	CharSequence text;
    	int duration = Toast.LENGTH_SHORT;
    	Toast toast;
    	// on bt click adds et contents to searchInput List
    	if (v.getId() == R.id.button) {
    		if (!searchInput.contains(et.getText().toString())){
    			if (ingredients.contains(et.getText().toString())) {
	    			searchInput.add(et.getText().toString());
	        		Log.d("PQ", "Adding ingredient: " + et.getText());
	        		et.setText("");
    			}
    			else {
    				text = "Please enter a valid ingredient.";
        			toast = Toast.makeText(context, text, duration);
        			toast.show();
    			}
    		}
    		else {
    			text = "You have already entered " + et.getText().toString();
    			toast = Toast.makeText(context, text, duration);
    			toast.show();
    		}
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
    		else {
    			text = "Please enter at least one ingredient " + et.getText().toString();
    			toast = Toast.makeText(context, text, duration);
    			toast.show();
    		}
    	}
    	// clicked Login
    	else if (v.getId() == R.id.bt_login) {
    		String username = tUsr.getText().toString();
    		String password = tPass.getText().toString();
    		// log in and change the login screen
    		// set tvLogin to say the account name
    		tvLogin.setText("Welcome, " + username + ", to PantryQuest!");
    		// hide the old elements
    		tUsr.setVisibility(View.GONE);
    		tPass.setVisibility(View.GONE);
    		btLog.setVisibility(View.GONE);
    		btReg.setVisibility(View.GONE);
    		// show hidden elements
    		btOut.setVisibility(View.VISIBLE);
    	}
    	// clicked Register
    	else if (v.getId() == R.id.bt_register) {
    		// hide the old elements
    		tUsr.setVisibility(View.GONE);
    		tPass.setVisibility(View.GONE);
    		btLog.setVisibility(View.GONE);
    		btReg.setVisibility(View.GONE);
    		tvLogin.setText("Please enter new account information.");
    		// show hidden elements
    		btRegSub.setVisibility(View.VISIBLE);
    		rName.setVisibility(View.VISIBLE);
    		rUsr.setVisibility(View.VISIBLE);
    		rPass.setVisibility(View.VISIBLE);
    	}
    	// clicked logout
    	else if (v.getId() == R.id.bt_logout) {
    		// revert to before logging in
    		btOut.setVisibility(View.GONE);
    		tUsr.setVisibility(View.VISIBLE);
    		tPass.setVisibility(View.VISIBLE);
    		btLog.setVisibility(View.VISIBLE);
    		btReg.setVisibility(View.VISIBLE);
    		tvLogin.setText("Welcome, Guest, to PantryQuest!");
    	}
    	// clicked submit on register screen
    	else if (v.getId() == R.id.bt_regSubmit) {
    		// revert back to before registering
    		btRegSub.setVisibility(View.GONE);
    		rName.setVisibility(View.GONE);
    		rUsr.setVisibility(View.GONE);
    		rPass.setVisibility(View.GONE);
    		tUsr.setVisibility(View.VISIBLE);
    		tPass.setVisibility(View.VISIBLE);
    		btLog.setVisibility(View.VISIBLE);
    		btReg.setVisibility(View.VISIBLE);
    		tvLogin.setText("Welcome, Guest, to PantryQuest!");
    	}
    }
    
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
    		jsonIngredients = new JSONArray(string);
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
   	// remove searchInput element on click
	@Override
	public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
		searchInput.remove(position);
		adapter.notifyDataSetChanged();
	}
	
}
