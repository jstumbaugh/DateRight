/**
 * Class for date plans
 * will create structure for date plan objects to exist in
 * */
package com.cse3345.dateright;

public class DatePlan {
	private String name;
	private String time;
	private String desc;
	
	public DatePlan(String name, String time, String desc) {
		this.name = name;
		this.time = time;
		this.desc = desc;
	}
	
	public String getName(){
		return name;
	}
	
	public String getTime(){
		return time;
	}
	
	public String getDesc(){
		return desc;
	}
	
	public String toString(){
		return name;
	}
}
