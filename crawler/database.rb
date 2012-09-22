require 'sequel'

module LifeLog
  class DBBase
    def initialize(params, table)
      @table = table
      @db = Sequel.connect(
        "mysql://#{params[:user]}:#{params[:pass]}@#{params[:host]}/#{params[:dbname]}",
        {:encoding => params[:encoding]}
      )
      @dataset = @db[@table.to_sym]
    end

    def unique_columns
      @db.indexes(@table).each_value do |config|
        return config[:columns] if config[:unique]
      end
    end

    def unique_columns_in(data)
      unique_columns.inject({}) {|hash, col_sym| hash[col_sym] = data[col_sym]; hash}
    end

    def create(&block)
      @db.create_table?(@table, {}, &block)
    end
  end

  class DB < DBBase
    def initialize(params, table)
      super
    end

    def method_missing(name, *args)
      get(args[0]).get(name)
    end

    # select
    def get(cond = nil)
      cond.nil? ? @dataset.all : @dataset.where(cond)
    end

    # select(not conditions)
    def unget(cond)
      @dataset.exclude(cond)
    end

    # insert or update
    def set(data)
      result = exist?(data) ? insert(data) : update(data)
      yield result if block_given?
    end

    def exist?(data)
      unique_data = unique_columns_in data
      unique_data.empty? ? true : select(unique_data).count == 0
    end

    def select(data)
      get(data)
    end

    def insert(data)
      @dataset << data
      self.current_method
    end

    def update(data)
      select(unique_columns_in data).update(data)
      self.current_method
    end

    def delete(cond = nil)
      cond.nil? ? @dataset.delete : @dataset.where(cond).delete
    end
  end
end

class Object
  def current_method
    $1 if /`(.*)'/ =~ caller.first
  end
end