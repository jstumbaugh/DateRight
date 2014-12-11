package com.cse3345.dateright;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

public class DatePlanActivity extends Activity {
	private TextView nameText;
	private TextView timeText;
	private TextView descText;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_date_plan);
		
		/**
		 * Catch intent **/
		Intent intent = getIntent();
		String name = intent.getStringExtra("name");
		String time = intent.getStringExtra("time");
		String desc = intent.getStringExtra("desc");
		
		//System.out.println(name + time + desc);
		nameText = (TextView) findViewById(R.id.dateName);
		nameText.setText(name);
		
		timeText = (TextView) findViewById(R.id.dateTime);
		timeText.setText(time);
		
		descText = (TextView) findViewById(R.id.dateDesc);
		descText.setText(desc);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.date_plan, menu);
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
}
