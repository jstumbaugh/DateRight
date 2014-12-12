package com.cse3345.dateright;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONObject;

public class UserActions {
	private JSONParser jsonParser;
	private JSONArrayParser jsonArrayParser;
	private static String loginURL = "http://54.69.57.226/dateright/api/login";
	//private static String logoutURL = "http://54.69.57.226/dateright/api/logout";
	private static String randomURL = "http://54.69.57.226/dateright/api/getRandomIdea";
	private static String searchURL = "http://54.69.57.226/dateright/api/searchDateplans";
	private static String registerURL = "http://54.69.57.226/dateright/api/createAccount";
	private static String userDatesURL = "http://54.69.57.226/dateright/api/viewUserDatePlans";
	
	public static String KEY_fName = "fname";
	public static String KEY_lName = "lname";
	public static String KEY_email = "email";
	public static String KEY_password = "pw";
	public static String KEY_userID = "userID";
	public static String KEY_username = "username";

	
	public UserActions() {
		jsonParser = new JSONParser();
		jsonArrayParser = new JSONArrayParser();
	}
	
	// Log in user
	public JSONObject loginUser(String email, String password) {
		Map<String,String> params = new HashMap();
		params.put("email", email);
		params.put("password", password);
		JSONObject json = jsonParser.getJSONFromUrl(loginURL, params);
		return json;
	}
	
	// random date plan
	public JSONArray randomDate(){
		//no parameters
		JSONArray json = jsonArrayParser.getJSONFromUrl(randomURL, null);
		return json;
	}
	
	// search date plans
	public JSONObject searchDates(String search){
		Map<String,String> params = new HashMap();
		params.put("SearchQuery", search);
		JSONObject json = jsonParser.getJSONFromUrl(searchURL, params);
		return json;
	}
	
	public JSONArray grabDates(String userId) {
		Map<String,String> params = new HashMap();
		params.put("UserID", userId);
		JSONArray json = jsonArrayParser.getJSONFromUrl(userDatesURL, params);
		return json;
	}
	
	// create account
	public JSONObject register(String email, String username, String pass, String fName, String lName, String sex, String securityAnswer){
		Map<String,String> params = new HashMap();
		//input values
		params.put("email", email);
		params.put("userName", username);
		params.put("password", pass);
		params.put("fName", fName);
		params.put("lName", lName);
		params.put("sex", sex);
		params.put("secAnswer", securityAnswer);
		//non variable paramaters
		params.put("userType", "1");
		params.put("securityQuestion", "1");
		JSONObject json= jsonParser.getJSONFromUrl(registerURL, params);
		return json;
	}
	
	/**
	// Log out user
	public JSONObject signOut() {
		// getting JSON Object
		JSONObject json = jsonParser.getJSONFromUrl(logoutURL, null);
		MainActivity.session.logoutUser();
		// return json
		return json;
	} 
	*/
}
