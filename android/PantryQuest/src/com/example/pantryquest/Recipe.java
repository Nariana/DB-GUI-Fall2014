/*
 * Displays a page for the recipe whose information
 * was passed in the intent for the page. Also has a
 * button which, when pressed, returns to the Results
 * Activity.
 */
//TODO: implement New Search button
package com.example.pantryquest;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.concurrent.ExecutionException;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.method.ScrollingMovementMethod;
import android.util.Log;
import android.widget.ImageView;
import android.widget.TextView;

public class Recipe extends Activity {

	// img is the image for the recipe
	private ImageView img;
	// txtTitle is the textView containing the title of the recipe
	private TextView txtTitle;
	// txtSteps is the the textView containing the steps for the recipe
	private TextView txtSteps;
	// txtDesc is the textview containing the description of the recipe
	private TextView txtDesc;
	// message is a String[] built in Results that contains the recipe info
	private String recipeName;
	// recipeDesc is a string detailing the recipe's time, cals, etc. . .
	private String recipeDesc;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_recipe);
		
		txtTitle = (TextView) findViewById(R.id.title);
		txtDesc = (TextView) findViewById(R.id.description);
		txtSteps = (TextView) findViewById(R.id.steps);
		img = (ImageView) findViewById(R.id.image);
		
		// set text color
		txtTitle.setTextColor(Color.BLACK);
		txtDesc.setTextColor(Color.BLACK);
		txtSteps.setTextColor(Color.WHITE);
		
		// set ingredients and directions to scroll
		txtSteps.setMovementMethod(new ScrollingMovementMethod());
		
		// retrieve message info from the intent
		Intent intent = getIntent();
		recipeName = intent.getStringExtra(Results.RECIPE_INFO);
		
		// get recipe info from database
        callAPI a = new callAPI();
        try {
			a.execute().get();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		}
		
		// set the image
		//
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
	
    public void setUpRecipeInfo() {
    	Log.i("Recipe name", recipeName);
    	String string = "http://54.69.70.135/DB-GUI-Fall2014/api/index.php/getRecipe?recipeName=" + recipeName;
    	string = string.replace(' ', '+');
    	string = getStringFromUrl(string);
    	Log.i("Recipe", string);
    	// set the text for the different elements in the page, as well as the image
    	try {
    		JSONObject jsonRecipe = new JSONObject(string);
    		txtTitle.setText(jsonRecipe.optString("recipeName"));
    		recipeDesc = "Time: " + jsonRecipe.optInt("time");
    		recipeDesc = recipeDesc + ", Calories: " + jsonRecipe.optInt("calories");
    		recipeDesc = recipeDesc + ", Rating: " + jsonRecipe.optInt("rating");
    		txtDesc.setText(recipeDesc);
    		string = jsonRecipe.optString("ingredients") + "\n\n" + jsonRecipe.optString("instruction");
    		txtSteps.setText(string);
    		Log.i("Description: ", recipeDesc);
    		
    		// set the image
    		Bitmap bitmap = BitmapFactory.decodeStream((InputStream) new URL(jsonRecipe.optString("picture")).getContent());
    		img.setImageBitmap(bitmap);
    		
    	}
    	catch (JSONException e) {
    		e.printStackTrace();
    	} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
    }

    final class callAPI extends AsyncTask<Void, Void, Void> {
    	
		@Override
		protected Void doInBackground(Void... params) {
			setUpRecipeInfo();
			return null;
		}
    }
}

