# -*- coding: utf-8 -*-
require 'database'
require 'yaml'
require 'services/twitter'

module LifeLog
  VERSION = '0.0.1'
  TABLE_NAME = 'twitter'
  
  class << self
    # 初期処理
    def init
      path = File.dirname(__FILE__) + "/config/database.yml"
      params = symbolize_keys(YAML.load_file(path))
      @db = DB.new(params, TABLE_NAME)
    end
    
    # DB作成処理
    def migrate
      init
      @db.delete
      # schema definition
      @db.create do
        primary_key :id
        String :url
        String :tweet
        timestamp :created_at
        String :source
        index :tweet, :unique => true
        index :url, :unique => true
        index :created_at
      end
    end
    
    # 登録処理
    def save(data)
      @db.set(data) do |name|
        p name
      end
    end
    
    def crawler_all
      init
      twitter = LifeLog::Tweet.new
      1.upto(32) do |page|
        twitter.run(page, 200) do |timeline|
          save timeline
        end
      end
    end
    
    def crawler
      # TODO
    end
    
    
    def symbolize_keys(o)
      case o
      when Array
        o.map {|elem| symbolize_keys elem}
      when Hash
        Hash[
          o.map {|key, value|
            k = key.is_a?(String) ? key.to_sym : key
            v = symbolize_keys value
            [k,v]
          }]
      else
        o
      end
    end
    
  end
  
end
