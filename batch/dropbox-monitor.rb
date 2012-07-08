# -*- coding: utf-8 -*-
require 'fssm'
require 'logger'
require 'pathname'
require 'fileutils'
require 'rmagick'
require 'optparse'

# diarysys5 project root path
PROJECT_ROOT = File.dirname(File.expand_path($PROGRAM_NAME)) + "/../"
# path to diarysys5 upload directory
UPLOAD_DIR = PROJECT_ROOT + "/app/views/_public/img/upload/"
UPLOAD_DIR_THUMBNAIL = UPLOAD_DIR + "/thumbnail/"
UPLOAD_DIR_SMALL = UPLOAD_DIR + "/small/"
# file max-width or max-height
THUMBNAIL_WIDTH = 150
THUMBNAIL_HEIGHT = 150
SMALL_WIDTH = 300
SMALL_HEIGHT = 300

# Utils module
module Utils
  class << self
    def realpath(path)
      Pathname.new(path).cleanpath
    end
    
    def file?(e)
      !!(FileTest.exist?(e) rescue nil)
    end

    def directory?(e)
      !!(FileTest::directory?(e) rescue nil)
    end
  end
end

# fileupload by dropbox
class DropboxFileUpload
  include FileUtils
  
  def initialize
    @log = SimpleLogger.new(PROJECT_ROOT + "/log/diarysys.fileupload.log")
    @log.level = Logger::INFO
    chmod(0777, UPLOAD_DIR)
    chmod(0777, UPLOAD_DIR_THUMBNAIL)
    chmod(0777, UPLOAD_DIR_SMALL)
  end
  
  def copy(from_dropbox, debug)
    to_upload = Utils.realpath(UPLOAD_DIR) + File.basename(from_dropbox)
    super(from_dropbox, to_upload)
    @log.info "create: #{to_upload}"
    puts "create: #{to_upload}" if debug
  end
  
  def copy_to_thumbnail(from_dropbox, debug)
    to_thumbnail = Utils.realpath(UPLOAD_DIR_THUMBNAIL) + File.basename(from_dropbox)
    image = Magick::Image.read(from_dropbox).first
    image.resize_to_fill!(THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT)
    image.write to_thumbnail
    @log.info "create: #{to_thumbnail}"
    puts "create: #{to_thumbnail}" if debug
  end
  
  def copy_to_small(from_dropbox, debug)
    to_small = Utils.realpath(UPLOAD_DIR_SMALL) + File.basename(from_dropbox)
    image = Magick::Image.read(from_dropbox).first
    image.resize_to_fit!(SMALL_WIDTH, SMALL_HEIGHT)
    image.write to_small
    @log.info "create: #{to_small}"
    puts "create: #{to_small}" if debug
  end
  
  def remove(filename, debug)
    begin
      [UPLOAD_DIR, UPLOAD_DIR_SMALL, UPLOAD_DIR_THUMBNAIL].each do |dir|
        filepath = Utils.realpath(dir) + filename
        super(filepath)
        @log.info "delete: #{filepath}"
        puts "delete: #{filepath}" if debug
      end
    rescue => e
      @log.error e.message
      puts e.message if debug
    end
  end
end

# custom logger
class SimpleLogger < Logger
  def initialize(path)
    super(Utils.realpath(path))
  end
end

#------------------------------------------------------------------#
config = {}
OptionParser.new do |opt|
  opt.on('-d', '--dropbox-dir DROPBOX_DIR') {|v| config[:dropbox_dir] = v}
  opt.on('-p', '--print') {|boolean| config[:print] = boolean}
  opt.parse!
end

filetype = "**/*.{jpg,jpeg,gif,png,JPG,JPEG,GIF,PNG}"
dropbox_dir = config[:dropbox_dir]

# directory check
raise "Directory notfound: %s" % dropbox_dir unless Utils.directory? dropbox_dir

# file monitor
upload = DropboxFileUpload.new
FSSM.monitor(dropbox_dir, filetype) do
  create do |base, file|
    sleep(1)
    upload.copy File.join(base, file), config[:print]
    upload.copy_to_small File.join(base, file), config[:print]
    upload.copy_to_thumbnail File.join(base, file), config[:print]
  end
  
  delete do |base, file|
    upload.remove file, config[:print]
  end
end
