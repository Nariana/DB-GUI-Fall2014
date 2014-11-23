/*
 * Displays a page for the recipe whose information
 * was passed in the intent for the page. Also has a
 * button which, when pressed, returns to the Results
 * Activity.
 */
//TODO: implement New Search button
package com.example.pantryquest;

/* inclusions */
import java.io.InputStream;
import java.net.URL;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

public class Recipe extends Activity implements OnClickListener {

	// bt is the button that, when pressed, returns to Results
	private Button bt;
	// img is the image for the recipe
	private ImageView img;
	// txtTitle is the textView containing the title of the recipe
	private TextView txtTitle;
	// txtDesc is the textView containing the description of the recipe
	private TextView txtDesc;
	// txtSteps is the the textView containing the steps for the recipe
	private TextView txtSteps;
	// message is a String[] built in Results that contains the recipe info
	private String[] message;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_recipe);
		
		txtTitle = (TextView) findViewById(R.id.title);
		txtDesc = (TextView) findViewById(R.id.description);
		txtSteps = (TextView) findViewById(R.id.steps);
		img = (ImageView) findViewById(R.id.image);
		
		// set bt
		bt = (Button) findViewById(R.id.backButton);
		bt.setOnClickListener(this);
		
		// retrieve message info from the intent
		Intent intent = getIntent();
		message = intent.getStringArrayExtra(Results.RECIPE_INFO);
		
		// in the code below, replace message[x] with correct index
		// set all of the textviews and the imageview from passed strings
		// txtTitle.setText(message[x]
		// txtDesc.setText(message[x]
		// txtSteps.setText(message[x]
		
		// set the image
		//Bitmap bitmap = BitmapFactory.decodeStream((InputStream) new URL(message[x]).getContent());
		//img.setImageBitmap(bitmap);
	}

	public void onClick(View v) {
		// on bt click return to MainActivity
		if (v.getId() == R.id.backButton) {
			this.finish();
		}
	}
	
	@Override
    protected void onStart() {
    	super.onStart();
    	Log.d("PQ", "Recipe onStart() Log Message");
    }
    
   @Override
    protected void onResume() {
    	super.onResume();
    	Log.d("PQ", "Recipe onResume() Log Message");
    }
    
   @Override
    protected void onPause() {
    	super.onPause();
    	Log.d("PQ", "Recipe onPause() Log Message");
    }
   
   @Override
    protected void onStop() {
    	super.onStop();
    	Log.d("PQ", "Recipe onStop() Log Message");
    }
   
   @Override
    protected void onRestart() {
    	super.onRestart();
    	Log.d("PQ", "Recipe onRestart() Log Message");
    }
   
   @Override
    protected void onDestroy() {
    	super.onDestroy();
    	Log.d("PQ", "Recipe onDestroy() Log Message");
    }
}
