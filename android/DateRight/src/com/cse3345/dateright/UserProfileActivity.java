package com.cse3345.dateright;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;

public class UserProfileActivity extends Activity {
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	//Profile
	private TextView userNameText;
	private TextView emailText;
	private TextView firstNameText;
	private TextView lastNameText;
	//results
	private TextView results;
	
	private Context context;
	private ProgressDialog pd;
	//list 
	ListView dateList;
	//JSONArray of Dates
	JSONArray dateArray;
	List<DatePlan> dates;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_user_profile);
		
		//System.out.println("Username: " + MainActivity.getInstance().session.getUserDetails().get("username"));
		//System.out.println("First Name: " + MainActivity.getInstance().session.getUserDetails().get("fname"));
		//System.out.println("Last Name: " + MainActivity.getInstance().session.getUserDetails().get("lname"));
		//System.out.println("Email: " + MainActivity.getInstance().session.getUserDetails().get("email"));
		
		/**
		 * Catch intent **/
		Intent intent = getIntent();
		//message is login string
		String message = intent.getStringExtra(UserSearchActivity.EXTRA_MESSAGE);
		////////
		userNameText = (TextView) findViewById(R.id.userNameText);
		emailText = (TextView) findViewById(R.id.emailText);
		firstNameText = (TextView) findViewById(R.id.firstNameText);
		lastNameText = (TextView) findViewById(R.id.lastNameText);
		//set text fields to session info
		userNameText.setText(MainActivity.getInstance().session.getUserDetails().get("username"));
		emailText.setText(MainActivity.getInstance().session.getUserDetails().get("fname"));
		firstNameText.setText(MainActivity.getInstance().session.getUserDetails().get("lname"));
		lastNameText.setText(MainActivity.getInstance().session.getUserDetails().get("email"));
		
		results = (TextView) findViewById(R.id.results);
		
		context = UserProfileActivity.this;
		//Get ListView
		dateList = (ListView) findViewById(R.id.list);
		
		grabDatesAsync task = new grabDatesAsync();
		task.execute((Object[]) null);
		
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

	private class grabDatesAsync extends AsyncTask<Object, Object, Object> {
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
			String userId;
			UserActions userFunction = new UserActions();
			JSONArray json = userFunction.grabDates("1");

			// check for login response
			try {
				System.out.println(json.getJSONArray(0).length());
				//System.out.println("Date Plans: " + json.getJSONArray("DatePlans"));
				if(json.getJSONArray(0).length() == 0){
					success = false;
				} else {
					success = true;
					JSONArray compile = new JSONArray();
					for(int i = 0; i < json.length(); i++){
						try {
							//System.out.println("Returned JSON Array[" + i + "]: " + json.getJSONArray(i).getJSONObject(0));
							compile.put(json.getJSONArray(i).getJSONObject(0));
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}
					System.out.println("compiled array: " + compile);
					dateArray = compile;
					//System.out.println("JSONArray: " + dateArray.toString());
				}
			} catch (JSONException e) {
				e.printStackTrace();
			}
			System.out.println("Success? " + success);
			return null;
		}

		@Override
		protected void onPostExecute(Object result) {
			if (pd != null) {
				pd.dismiss();
			}
			if (success) {
				//add stuff
				promptMessageUI("Success!");
				addDates();
			} else {
				//Progress dialog didn't work
				results.setText("No Results D:");
			}
		}
	}
	
	public void addDates() {
		dates = new ArrayList<DatePlan>();
		dates.clear();
		for(int i = 0; i < dateArray.length(); i++){
			try {
				DatePlan dp = new DatePlan(dateArray.getJSONObject(i).getString("Name"),
				dateArray.getJSONObject(i).getString("Timestamp"),
				dateArray.getJSONObject(i).getString("Description"));
				dates.add(dp);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		
        // Define a new Adapter
        // First parameter - Context
        // Second parameter - Layout for the row
        // Third parameter - ID of the TextView to which the data is written
        // Forth - the Array of data
        ArrayAdapter<DatePlan> adapter = new ArrayAdapter<DatePlan>(this,
          android.R.layout.simple_list_item_1, android.R.id.text1, dates);
        
        // Assign adapter to ListView
        dateList.setAdapter(adapter); 
        
        dateList.setOnItemClickListener(new OnItemClickListener(){
			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				for(int i = 0; i < dates.size(); i++){
					//System.out.println(dates.get(i).getName());
					//System.out.println(parent.getItemAtPosition(position).toString());
					if(dates.get(i).getName() == parent.getItemAtPosition(position).toString()){
						//System.out.println("match!");
						//System.out.println(dates.get(i).getName());
						//System.out.println(dates.get(i).getTime());
						//System.out.println(dates.get(i).getDesc());
						
						/**
						 * Launch Date Plan activity with required info passed as intent
						 * */
						Intent intent = new Intent(UserProfileActivity.this, DatePlanActivity.class);
						intent.putExtra("name", dates.get(i).getName());
						intent.putExtra("time", dates.get(i).getTime());
						intent.putExtra("desc", dates.get(i).getDesc());
						startActivity(intent);
					}
				}
				//.... This spits out string
				//System.out.println(parent.getItemAtPosition(position));
			}
        });
	}
	
	/*
    jsonParam.put("UserID", "1");
    */
}
