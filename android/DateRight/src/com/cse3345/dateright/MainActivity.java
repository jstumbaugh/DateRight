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
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

public class MainActivity extends Activity {
	protected static final String EXTRA_MESSAGE = "com.cse3345.DateRight.MESSAGE";
	//protected static final String EXTRA_MESSAGE2 = "com.cse3345.DateRight.MESSAGE2";
	//Search
	private EditText searchInput;
	private Button searchButton;
	//Login
	private Button randomButton;
	private EditText loginEmail;
	private EditText loginPass;
	private Button loginButton;
	//private String loginString;
	private Button continueLogin;
	
	//Create Account
	private EditText createAccountEmail;
	private EditText createAccountUserName;
	private EditText createAccountPass;
	private EditText createAccountFirstName;
	private EditText createAccountLastName;
	private RadioGroup sexRadioGroup;
	private RadioButton sexRadioButton;
	private EditText securityAnswer;
	private Button createAccountButton;
	private TextView securityQuestion;
	
	//Network variables
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	private TextView textView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		/**
		 * SEARCH BUTTON SECTION
		 * */
		searchInput = (EditText) findViewById(R.id.searchInput);
		searchButton = (Button) findViewById(R.id.searchButton);
		searchButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				Intent intent = new Intent(MainActivity.this, GuestSearchActivity.class);
				String message = searchInput.getText().toString();
				//System.out.println(message);
				intent.putExtra(EXTRA_MESSAGE, message);
				startActivity(intent);
			}
		});
		
		/**
		 * RANDOM BUTTON SECTION
		 * */
		randomButton = (Button) findViewById(R.id.randomButton);
		
		randomButton.setOnClickListener(new OnClickListener() {
			public void onClick(View view) {
				Intent intent = new Intent(MainActivity.this, DisplayRandomActivity.class);
				startActivity(intent);
			}
		});
		
		/**
		 * LOGIN SECTION
		 * */
		loginEmail = (EditText) findViewById(R.id.loginInputEmail);
		loginPass = (EditText) findViewById(R.id.loginInputPass);
		loginButton = (Button) findViewById(R.id.loginButton);
		continueLogin = (Button) findViewById(R.id.contLogin);
		
		//for debuging purposes
		textView = (TextView) findViewById(R.id.title);
		
		loginButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				//start login process
				getWebData();
			}
		});
		
		continueLogin.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				Intent profileIntent = new Intent(MainActivity.this, UserSearchActivity.class);
				profileIntent.putExtra(EXTRA_MESSAGE, textView.getText().toString());
				startActivity(profileIntent);
			}
		});
		
		/**
		 * CREAT ACCONT SECTION
		 * */
		createAccountEmail = (EditText) findViewById(R.id.createAccountInputEmail);
		createAccountUserName = (EditText) findViewById(R.id.createAccountInputUserName);
		createAccountPass = (EditText) findViewById(R.id.createAcountInputPass);
		createAccountFirstName = (EditText) findViewById(R.id.createAccountInputFirstName);
		createAccountLastName = (EditText) findViewById(R.id.createAccountInputLastName);
		sexRadioGroup = (RadioGroup) findViewById(R.id.sexRadio);
		//sexRadioButton;
		securityAnswer = (EditText) findViewById(R.id.createAccountInputSecurityQuestion);
		createAccountButton = (Button) findViewById(R.id.createAccountButton);
		
		securityQuestion = (TextView) findViewById(R.id.createAccountSecurityQuestion);
		
		createAccountButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				//get selected radio button from radioGroup
				int selectedId = sexRadioGroup.getCheckedRadioButtonId();
				sexRadioButton = (RadioButton) findViewById(selectedId);
				//start Create Account process
				getWebDataTwo();
			}
		});
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
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
	 * 	LOGIN PROCESS
	 * */
	public void getWebData() {
		String url = "http://54.69.57.226/dateright/api/login";
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
    	private final ProgressDialog dialog = new ProgressDialog(MainActivity.this);
    	
    	protected void onPreExecute(){
    		this.dialog.setMessage("Processing...");
    		this.dialog.show();
    	}
    	
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
			textView.setText(result);
			//System.out.println(textView.getText().toString());
    	   //loginString = new String(result);
    	   //System.out.println(loginString);
    	   this.dialog.dismiss();
           
           //System.out.println(result);
           //loginInfoResult = new String(result);
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
	        jsonParam.put("email", loginEmail.getText().toString());
	        jsonParam.put("password", loginPass.getText().toString());
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
	
	/** END LOGIN PROCESS */
	
	/**
	 * 	CREATE ACCOUNT PROCESS
	 * */
	public void getWebDataTwo() {
		String url = "http://54.69.57.226/dateright/api/createAccount";
		ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
		if(networkInfo != null && networkInfo.isConnected()) {
			new DownloadWebpageTaskTwo().execute(url);
		}
	}
	
    private class DownloadWebpageTaskTwo extends AsyncTask<String, Void, String> {
       @Override
       protected String doInBackground(String... urls) {
             
           // params comes from the execute() call: params[0] is the url.
           try {
               return downloadUrlTwo(urls[0]);
           } catch (IOException | JSONException e) {
               return "Unable to retrieve web page. URL may be invalid.";
           }
       }
       // onPostExecute displays the results of the AsyncTask.
       @Override
       protected void onPostExecute(String result) {
           textView.setText(result);
    	   System.out.println(result);
      }
   }
	
	// Given a URL, establishes an HttpUrlConnection and retrieves
	// the web page content as a InputStream, which it returns as
	// a string.
	private String downloadUrlTwo(String myurl) throws IOException, JSONException {
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
	        jsonParam.put("email", createAccountEmail.getText().toString());
	        jsonParam.put("userName", createAccountUserName.getText().toString());
	        jsonParam.put("password", createAccountPass.getText().toString());
	        jsonParam.put("fName", createAccountFirstName.getText().toString());
	        jsonParam.put("lName", createAccountLastName.getText().toString());
	        jsonParam.put("userType", "1");
	        jsonParam.put("sex", sexRadioButton.getText().toString());
	        jsonParam.put("securityQuestion", "1");
	        if(securityAnswer.getText().toString() == "Male"){
	        	jsonParam.put("securityAnswer", "0");
	        } else {
	        	jsonParam.put("securityAnswer", "1");
	        }
	        //System.out.println(jsonParam.toString());
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
	        else { 
	        	//When successful there is nothing on the page. 
	        	//Without for sure closing it will just look forever. LOL.
	        	is.close();
	        }
	    }
	}
	/** END CREATE ACCOUNT SECTION */
}
