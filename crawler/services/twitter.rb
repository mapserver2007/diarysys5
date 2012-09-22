# -*- coding: utf-8 -*-
require 'twitter'
require 'yaml'
require 'active_support'

module LifeLog
  class Tweet
    TWITTER_URL = 'http://twitter.com'
    TIMELINE_URL = "#{TWITTER_URL}/%s/status/%s"

    def initialize
      path = File.dirname(__FILE__) + "/../config/twitter.yml"
      params = YAML.load_file(path)
      auth(params)
      @client = Twitter::Client.new
    end
    
    def auth(params)
      Twitter.configure do |config|
        config.consumer_key = params["consumer_key"]
        config.consumer_secret = params["consumer_secret"]
        config.oauth_token = params["oauth_token"]
        config.oauth_token_secret = params["oauth_token_secret"]
      end
    end
    
    def run(page = 1, count = 10)
      tweets = @client.user_timeline("mapserver2007", {
        :include_rts => true,
        :count => count,
        :page => page
      })
      timelines = tweets.each_with_object [] do |tweet, list|
        list << {
          :url => TIMELINE_URL % ["mapserver2007", tweet.id],
          :tweet => tweet.text,
          :created_at => tweet.created_at,
          :source => /<.*?>(.*?)<\/.*?>/ =~ tweet.source ? $1 : nil
        }
      end
      timelines.each {|data| yield data}
    end
    
  end
end

