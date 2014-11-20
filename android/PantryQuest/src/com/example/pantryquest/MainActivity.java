package com.example.pantryquest;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;
import org.xmlpull.v1.XmlPullParserFactory;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;

//REST API implementation from guide at:
//http://blog.strikeiron.com/bid/73189/Integrate-a-REST-API-into-Android-Application-in-less-than-15-minutes
final class CallAPI extends AsyncTask<String, String, String> {
	@Override
	protected String doInBackground(String... params) {
		CallAPIResult result = null;
		String urlString=params[0];
		String resultToDisplay="";
		InputStream in = null;
		// Http Get
		try {
			URL url = new URL(urlString);
			HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
			in = new BufferedInputStream(urlConnection.getInputStream());
		}
		catch (Exception e) {
			System.out.println(e.getMessage());
			return e.getMessage();
		}
		//parse XML
		XmlPullParserFactory pullParserFactory;
		try {
			pullParserFactory = XmlPullParserFactory.newInstance();
			XmlPullParser parser = pullParserFactory.newPullParser();
			parser.setFeature(XmlPullParser.FEATURE_PROCESS_NAMESPACES, false);
			parser.setInput(in, null);
			result = parseXML(parser);
		}
		catch (XmlPullParserException e) {
			e.printStackTrace();
		}
		catch (IOException e) {
			e.printStackTrace();
		}
		
		return resultToDisplay;
	}
	
	//function to parse XML, (Would try-catch blocks be better than throwing?)
	private CallAPIResult parseXML(XmlPullParser parser) throws XmlPullParserException, IOException {
		int eventType = parser.getEventType();
		CallAPIResult result = new CallAPIResult();
		
		while(eventType != XmlPullParser.END_DOCUMENT) {
			String name = null;
			switch (eventType) 
			{
			case XmlPullParser.START_TAG:
				name = parser.getName();
				if (name.equals("Error")) {
					System.out.println("Web API Error!");
				}
				else if (name.equals("StatusNbr")) {
					result.statusNbr = parser.nextText();
				}
				break;
			case XmlPullParser.END_TAG:
				break;
			}
			eventType = parser.next();
		}
		return result;
	}
	protected void onPostExecute(String result) {
		
	}
}

//object to store parsed xml results
final class CallAPIResult {
	public String statusNbr;
}

public class MainActivity extends Activity {

	//public final static String userName;
	//public final static String password;
	//public final static String apiURL;
	
	/*
	 * example call
	 * String urlString = ...
	 * new CallAPI().execute(urlString);
	 */
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d("PQ", "onCreate() Log Message");
        setContentView(R.layout.activity_main);
    }

    @Override
    protected void onStart() {
    	super.onStart();
    	Log.d("PQ", "onStart() Log Message");
    }
    
   @Override
    protected void onResume() {
    	super.onResume();
    	Log.d("PQ", "onResume() Log Message");
    }
    
   @Override
    protected void onPause() {
    	super.onPause();
    	Log.d("PQ", "onPause() Log Message");
    }
   
   @Override
    protected void onStop() {
    	super.onStop();
    	Log.d("PQ", "onStop() Log Message");
    }
   
   @Override
    protected void onRestart() {
    	super.onRestart();
    	Log.d("PQ", "onRestart() Log Message");
    }
   
   @Override
    protected void onDestroy() {
    	super.onDestroy();
    	Log.d("PQ", "onDestroy() Log Message");
    }
}
