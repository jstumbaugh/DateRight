package com.cse3345.dateright;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class UserSearchActivity extends Activity {
	protected static final String EXTRA_MESSAGE = "com.cse3345.DateRight.MESSAGE";
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	private TextView debug;
	
	//Search
	private EditText searchInput;
	private Button	searchButton;
	private TextView results;
	
	//Profile
	private Button viewProfile;
	private String loginInfo;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_user_search);
		
		/**
		 * Catch intent **/
		Intent intent = getIntent();
		//message is login string
		String message = intent.getStringExtra(MainActivity.EXTRA_MESSAGE);
		System.out.println(message);
		debug = (TextView) findViewById(R.id.debug);
		debug.setText(message);
		
		searchInput = (EditText) findViewById(R.id.searchInput);
		searchButton = (Button) findViewById(R.id.searchButton);
		results = (TextView) findViewById(R.id.results);
		
		searchButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				getWebData();
			}
		});
		
		viewProfile = (Button) findViewById(R.id.profileButton);
		viewProfile.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				Intent profileIntent = new Intent(UserSearchActivity.this, UserProfileActivity.class);
				profileIntent.putExtra(EXTRA_MESSAGE, debug.getText().toString());
				startActivity(profileIntent);
			}
		});
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.user_search, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
	
	/**
	 * 	SEARCH PROCESS
	 * */
	public void getWebData() {
		String url = "http://54.69.57.226/dateright/api/searchDateplans";
		ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
		if(networkInfo != null && networkInfo.isConnected()) {
			new DownloadWebpageTask().execute(url);
		}
	}
	
	// Uses AsyncTask to create a task away from the main UI thread. This task takes a 
    // URL string and uses it to create an HttpUrlConnection. Once the connection
    // has been established, the AsyncTask downloads the contents of the webpage as
    // an InputStream. Finally, the InputStream is converted into a string, which is
    // displayed in the UI by the AsyncTask's onPostExecute method.
    private class DownloadWebpageTask extends AsyncTask<String, Void, String> {
       @Override
       protected String doInBackground(String... urls) {
             
           // params comes from the execute() call: params[0] is the url.
           try {
               return downloadUrl(urls[0]);
           } catch (IOException | JSONException e) {
               return "Unable to retrieve web page. URL may be invalid.";
           }
       }
       // onPostExecute displays the results of the AsyncTask.
       @Override
       protected void onPostExecute(String result) {
           //textView.setText(result);
           System.out.println(result);
           results.setText(result);
      }
   }
	
	// Given a URL, establishes an HttpUrlConnection and retrieves
	// the web page content as a InputStream, which it returns as
	// a string.
	private String downloadUrl(String myurl) throws IOException, JSONException {
	    InputStream is = null;
	    // Only display the first 500 characters of the retrieved
	    // web page content.
	    int len = 500;
	        
	    try {
	        URL url = new URL(myurl);
	        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
	        conn.setReadTimeout(10000 /* milliseconds */);
	        conn.setConnectTimeout(15000 /* milliseconds */);
	        conn.setRequestMethod("POST");
	        conn.setDoInput(true);
	        conn.setDoOutput(true);
	        conn.setRequestProperty("Content-Type", "application/json");
	        // Starts the query
	        conn.connect();
	        //Create JSONObject here
	        JSONObject jsonParam = new JSONObject();
	        jsonParam.put("SearchQuery", searchInput.getText().toString());
	        OutputStream out = conn.getOutputStream();
	        out.write(jsonParam.toString().getBytes("UTF-8"));
	        out.flush();
	        out.close();
	        int response = conn.getResponseCode();
	        Log.d(DEBUG_TAG, "The response is: " + response);
	        is = conn.getInputStream();

	        // Convert the InputStream into a string
	        String contentAsString = readIt(is, len);
	        return contentAsString;
	        
	    // Makes sure that the InputStream is closed after the app is
	    // finished using it.
	    } finally {
	        if (is != null) {
	            is.close();
	        } 
	    }
	}
	
	// Reads an InputStream and converts it to a String.
	public String readIt(InputStream stream, int len) throws IOException, UnsupportedEncodingException {
	    Reader reader = null;
	    reader = new InputStreamReader(stream, "UTF-8");        
	    char[] buffer = new char[len];
	    reader.read(buffer);
	    return new String(buffer);
	}
	
	/** END SEARCH PROCESS */
}
