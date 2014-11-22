/*
 * Displays a page for the recipe whose information
 * was passed in the intent for the page. Also has a
 * button which, when pressed, returns to the Results
 * Activity.
 */
package com.example.pantryquest;

/* inclusions */
import android.app.Activity;
import android.os.Bundle;
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
		
		// set bt
		bt = (Button) findViewById(R.id.backButton);
		bt.setOnClickListener(this);
		
		// retrieve message info from the intent
		Intent intent = getIntent();
		
	}

	public void onClick(View v) {
		// on bt click return to MainActivity
		if (v.getId() == R.id.backButton) {
			this.finish();
		}
	}
}
