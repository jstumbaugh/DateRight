package com.cse3345.dateright;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.cse3345.dateright.MainActivity;

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
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.TextView;
import android.widget.Toast;

public class GuestSearchActivity extends Activity {
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	//private TextView textView;
	//Search
	private EditText searchInput;
	private Button	searchButton;
	private TextView results;
	
	private Context context;
	private ProgressDialog pd;
	//string
	private String searchQuery;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_guest_search);
		
		/**
		 * Catch intent **/
		Intent intent = getIntent();
		String message = intent.getStringExtra(MainActivity.EXTRA_MESSAGE);
		
		context = GuestSearchActivity.this;
		
		
		//textView = (TextView) findViewById(R.id.debug);
		//textView.setText(message);
		//System.out.println(message);
		/*///////////////////////////////////////
		 * Search by message sent from main activity
		 * */
		searchQuery = message;
		System.out.println("Search String: " + searchQuery);
		if(searchQuery.length() == 0){
			promptMessageUI("Try typing in a word to search first.");
		} else {
			searchAsync task = new searchAsync();
			task.execute((Object[]) null);
		}
		///////////////////////////////////////////
		
		searchInput = (EditText) findViewById(R.id.searchInput);
		//Take intent message and place in search field
		searchInput.setText(message);
		//search for a specific thing
		searchButton = (Button) findViewById(R.id.searchButton);
		results = (TextView) findViewById(R.id.results);
		
		searchButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				searchQuery = searchInput.getText().toString();
				System.out.println("Search String: " + searchQuery);
				if(searchQuery.length() == 0){
					promptMessageUI("Try typing in a word to search first.");
				} else {
					searchAsync task = new searchAsync();
					task.execute((Object[]) null);
				}
			}
		});
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.guest_search, menu);
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
	
	/*
	 * Display prompt for user to input text
	 */
	public void promptMessageUI(final String message) {
		runOnUiThread(new Runnable() {

			@Override
			public void run() {
				Toast.makeText(context, message, Toast.LENGTH_LONG).show();
			}
		});
	}
	
	private class searchAsync extends AsyncTask<Object, Object, Object> {
		private boolean success = false;

		@Override
		protected void onPreExecute() {
			pd = new ProgressDialog(context);
			pd.setTitle("Searching Date Plans....");
			pd.setMessage("Please wait.");
			pd.setCancelable(false);
			pd.setIndeterminate(true);
			pd.show();
		}

		@Override
		protected Object doInBackground(Object... params) {
			UserActions userFunction = new UserActions();
			JSONObject json = userFunction.searchDates(searchQuery);

			// check for login response
			try {

				if (json.get("results") == null) {
					// Error in login, signal post execute there was a problem
					success = false;
				} else {
					System.out.println("Date Plans: " + json.getJSONArray("DatePlans"));
					if(json.getJSONArray("DatePlans").length() == 0){
						success = false;
					} else {
						success = true;
						addDates(json.getJSONArray("DatePlans"));
					}
				}
			} catch (JSONException e) {
				e.printStackTrace();
			}
			return null;
		}

		@Override
		protected void onPostExecute(Object result) {
			if (pd != null) {
				pd.dismiss();
			}
			if (success) {
				// Create login session in shared preferences
				promptMessageUI("Success!");
			} else {
				//Progress dialog didn't work
				results.setText("No Results D:");
			}
		}
	}
	
	public void addDates(JSONArray dateArray) {
		//System.out.println("Length: " + dateArray.length());
		//grap Date Object
		//JSONObject j = dateArray.getJSONObject(i);
		//System.out.println("Name: " + j.getString("Name"));
		//System.out.println("Time: " + j.getString("Timestamp"));
		//System.out.println("Description: " + j.getString("Description"));
		
	}
}
