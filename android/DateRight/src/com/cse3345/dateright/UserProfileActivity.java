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
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

public class UserProfileActivity extends Activity {
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	//Profile
	//private JSONObject profileInfo;
	private TextView profileTitle;
	private TextView userNameText;
	private TextView emailText;
	private TextView firstNameText;
	private TextView lastNameText;
	private TextView sexText;
	//Date Plans
	//results
	private TextView results;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_user_profile);
		
		/**
		 * Catch intent **/
		Intent intent = getIntent();
		//message is login string
		String message = intent.getStringExtra(UserSearchActivity.EXTRA_MESSAGE);
		//System.out.println(message);
		
		profileTitle = (TextView) findViewById(R.id.profileTitle);
		profileTitle.setText(message);
		
		results = (TextView) findViewById(R.id.results);
		
		getWebData();
		
		/*try {
			profileInfo = new JSONObject(message);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println("JSON Error Yo");
		}
		
		userNameText = (TextView) findViewById(R.id.userNameText);
		emailText = (TextView) findViewById(R.id.emailText);
		firstNameText = (TextView) findViewById(R.id.firstNameText);
		lastNameText = (TextView) findViewById(R.id.lastNameText);
		sexText = (TextView) findViewById(R.id.sexText);
		
		//set values in JSONObject to text in text views
		try {
			userNameText.setText(profileInfo.get("UserName").toString());
			emailText.setText(profileInfo.get("Email").toString());
			firstNameText.setText(profileInfo.get("FirstName").toString());
			lastNameText.setText(profileInfo.get("LastName").toString());
			sexText.setText(profileInfo.get("Sex").toString());
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		*/
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.user_profile, menu);
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
	 * 	DATE PLANS PROCESS
	 * */
	public void getWebData() {
		String url = "http://54.69.57.226/dateright/api/viewUserDatePlans";
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
	        jsonParam.put("UserID", "1");
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
	
	/** END DATE PLAN PROCESS */
}
